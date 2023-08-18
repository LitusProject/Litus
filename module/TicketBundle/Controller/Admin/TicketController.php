<?php

namespace TicketBundle\Controller\Admin;

use CommonBundle\Component\Util\File\TmpFile;
use CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile;
use DateTime;
use Laminas\Http\Headers;
use Laminas\Mail\Message;
use Laminas\Mime\Mime;
use Laminas\Mime\Part;
use Laminas\View\Model\ViewModel;
use TicketBundle\Component\Document\Generator\Event\Csv as CsvGenerator;
use TicketBundle\Component\Document\Generator\Event\Pdf as PdfGenerator;
use TicketBundle\Entity\Event;
use TicketBundle\Entity\Ticket;

/**
 * TicketController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class TicketController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $event = $this->getEventEntity();
        if ($event === null) {
            return new ViewModel();
        }

        if ($this->getParam('field') !== null) {
            $tickets = $this->search($event);
        }

        if (!isset($tickets)) {
            $tickets = $this->getEntityManager()
                ->getRepository('TicketBundle\Entity\Ticket')
                ->findAllActiveByEvent($event);
        }

        $paginator = $this->paginator()->createFromArray(
            $tickets,
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'event'             => $event,
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function exportAction()
    {
        $event = $this->getEventEntity();
        if ($event === null) {
            return new ViewModel();
        }

        $file = new CsvFile();
        $document = new CsvGenerator($this->getEntityManager(), $event);
        $document->generateDocument($file);

        $now = new DateTime();
        $filename = 'tickets_' . $now->format('Y_m_d') . '.csv';

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Content-Type'        => 'text/csv',
            )
        );
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $file->getContent(),
            )
        );
    }

    public function printAction()
    {
        $event = $this->getEventEntity();
        if ($event === null) {
            return new ViewModel();
        }

        $file = new TmpFile();
        $document = new PdfGenerator($this->getEntityManager(), $event, $file);
        $document->generate();

        $now = new DateTime();
        $filename = 'tickets_' . $now->format('Y_m_d') . '.pdf';

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Content-Type'        => 'application/pdf',
            )
        );
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $file->getContent(),
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        $event = $this->getEventEntity();
        if ($event === null) {
            return new ViewModel();
        }

        $tickets = $this->search($event);

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        array_splice($tickets, $numResults);

        $result = array();
        foreach ($tickets as $ticket) {
            $item = (object) array();
            $item->rNumber = $ticket->getUniversityIdentification();
            $item->person = $ticket->getFullName() ? $ticket->getFullName() : '(none)';
            $item->status = $ticket->getStatus();
            $item->email = $ticket->getEmail();
            $item->organization = $ticket->getOrganization();
            $item->option = ($ticket->getOption() ? $ticket->getOption()->getName() : '') . ' ' . ($ticket->isMember() ? 'Member' : 'Non Member');
            $item->number = $ticket->getNumber();
            $item->orderId = $ticket->getOrderId();
            $item->bookDate = $ticket->getBookDate() ? $ticket->getBookDate()->format('d/m/Y H:i') : '';
            $item->soldDate = $ticket->getSoldDate() ? $ticket->getSoldDate()->format('d/m/Y H:i') : '';
            $item->isMember = $ticket->isMember();
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    public function csvAction()
    {
        $form = $this->getForm('ticket_ticket_csv');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $fileData = $this->getRequest()->getFiles();

            $fileName = $fileData['file']['tmp_name'];

            $ticketArray = array();

            $open = fopen($fileName, 'r');
            if ($open != false) {
                $data = fgetcsv($open, 10000, ',');

                while ($data !== false) {
                    $ticketArray[] = $data;
                    $data = fgetcsv($open, 10000, ',');
                }
                fclose($open);
            }

            $form->setData($formData);

            if ($form->isValid()) {
                $count = 0;
                $qrSend = 0;
                foreach ($ticketArray as $data) {
                    if (in_array(null, array_slice($data, 0, 9))) {
                        continue;
                    }

                    $ticket = $this->getEntityManager()
                        ->getRepository('TicketBundle\Entity\Ticket')
                        ->findOneBy(
                            array(
                                'orderId'   => $data[1],  //orderId
                                'invoiceId' => $data[22], //invoiceId
                            )
                        );

                    if ($ticket !== null && $ticket->getEvent()->getId() === $this->getEventEntity()->getId() && ($data[3] === '9' || $data[3] === '5')) {
                        if ($ticket->getStatus() !== 'Sold') {
                            $ticket->setStatus('sold');
                            if ($ticket->getEvent()->getQrEnabled()) {
                                $ticket->setQrCode();
                                $this->sendQrMail($ticket);
                                $qrSend += 1;
                            }
                            $this->getEntityManager()->flush();
                            $count += 1;
                        }
                    }
                }

                $this->flashMessenger()->success(
                    'Succes',
                    $count . ' tickets set as sold and ' . $qrSend . ' qr codes send'
                );

                $this->redirect()->toRoute(
                    'ticket_admin_ticket',
                    array(
                        'action' => 'manage',
                        'id' => $this->getEventEntity()->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    /**
     * @param  Event $event
     * @return array|null
     */
    private function search(Event $event)
    {
        switch ($this->getParam('field')) {
            case 'person':
                return $this->getEntityManager()
                    ->getRepository('TicketBundle\Entity\Ticket')
                    ->findAllByEventAndPersonName($event, $this->getParam('string'));
            case 'option':
                return $this->getEntityManager()
                    ->getRepository('TicketBundle\Entity\Ticket')
                    ->findAllByEventAndOption($event, $this->getParam('string'));
            case 'organization':
                return $this->getEntityManager()
                    ->getRepository('TicketBundle\Entity\Ticket')
                    ->findAllByEventAndOrganization($event, $this->getParam('string'), $this->getCurrentAcademicYear());
        }
    }

    /**
     * @return Event|null
     */
    private function getEventEntity()
    {
        $event = $this->getEntityById('TicketBundle\Entity\Event');

        if (!($event instanceof Event)) {
            $this->flashMessenger()->error(
                'Error',
                'No event was found!'
            );

            $this->redirect()->toRoute(
                'ticket_admin_event',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $event;
    }

    private function sendQrMail(Ticket $ticket)
    {
        $event = $ticket->getEvent();
        $language = $this->getLanguage();

        $entityManager = $this->getEntityManager();
        if ($language === null) {
            $language = $entityManager->getRepository('CommonBundle\Entity\General\Language')
                ->findOneByAbbrev('en');
        }

        $mailData = unserialize(
            $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('ticket.subscription_mail_data')
        );

        $message = $mailData[$language->getAbbrev()]['content'];
        $subject = str_replace('{{event}}', $event->getActivity()->getTitle($language), $mailData[$language->getAbbrev()]['subject']);

        $mailAddress = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('ticket.subscription_mail');

        $mailName = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('ticket.subscription_mail_name');

        $url = $this->url()
            ->fromRoute(
                'ticket',
                array('action' => 'qr',
                    'id'       => $event->getRandId(),
                    'qr'     => $ticket->getQrCode()
                ),
                array('force_canonical' => true)
            );

        $url = str_replace('leia.', '', $url);

        $qrSource = str_replace(
            '{{encodedUrl}}',
            urlencode($url),
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.google_qr_api')
        );

        $message = str_replace('{{event}}', $event->getActivity()->getTitle($language), $message);
        $message = str_replace('{{eventDate}}', $event->getActivity()->getStartDate()->format('d/m/Y'), $message);
        $message = str_replace('{{qrSource}}', $qrSource, $message);
        $message = str_replace('{{qrLink}}', $url, $message);
        $message = str_replace('{{actiMail}}', $mailAddress, $message);
        $message = str_replace('{{ticketOption}}', $ticket->getOption()->getName(), $message);

        $part = new Part($message);

        $part->type = Mime::TYPE_HTML;
        $part->charset = 'utf-8';
        $newMessage = new \Laminas\Mime\Message();
        $newMessage->addPart($part);
        $mail = new Message();
        $mail->setEncoding('UTF-8')
            ->setBody($newMessage)
            ->setFrom($mailAddress, $mailName)
            ->addTo($ticket->getEmail(), $ticket->getFullName())
            ->setSubject($subject)
            ->addBcc('it@vtk.be')
            ->addBcc($mailAddress);
        if (getenv('APPLICATION_ENV') != 'development') {
            $this->getMailTransport()->send($mail);
        }
    }
}

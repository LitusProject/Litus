<?php

namespace TicketBundle\Controller\Sale;

use Laminas\Mail\Message;
use Laminas\Mime\Mime;
use Laminas\Mime\Part;
use Laminas\View\Model\ViewModel;
use TicketBundle\Entity\Event;
use TicketBundle\Entity\Ticket;

/**
 * TicketController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class TicketController extends \TicketBundle\Component\Controller\SaleController
{
    public function overviewAction()
    {
        $event = $this->getEntityManager()
            ->getRepository('TicketBundle\Entity\Event')
            ->findOneById($this->getParam('id'));

        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('TicketBundle\Entity\Ticket')
                ->findAllActiveByEvent($event),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'academicYear'      => $this->getCurrentAcademicYear(),
            )
        );
    }

    public function unassignAction()
    {
        $this->initAjax();

        $ticket = $this->getTicketEntity();
        if ($ticket === null) {
            return new ViewModel();
        }

        $ticket->setStatus('empty');
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $ticket = $this->getTicketEntity();
        if ($ticket === null) {
            return new ViewModel();
        }

        if ($ticket->getEvent()->areTicketsGenerated()) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($ticket);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function saleAction()
    {
        $this->initAjax();

        $ticket = $this->getTicketEntity();
        if ($ticket === null) {
            return new ViewModel();
        }
        $ticket->setStatus('sold');
        if ($ticket->getEvent()->getQrEnabled()) {
            $ticket->setQrCode();
            $this->sendQrMail($ticket);
        }
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function undoSaleAction()
    {
        $this->initAjax();

        $ticket = $this->getTicketEntity();
        if ($ticket === null) {
            return new ViewModel();
        }

        $ticket->setStatus('booked');
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
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
            $item->id = $ticket->getId();
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
            $item->rNumber = $ticket->getUniversityIdentification();
            $item->price = $ticket->getPrice();
            $item->qrCode = $ticket->getQrCode();
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    /**
     * @return Ticket|null
     */
    private function getTicketEntity()
    {
        $ticket = $this->getEntityById('TicketBundle\Entity\Ticket', 'ticket');

        if (!($ticket instanceof Ticket)) {
            $this->flashMessenger()->error(
                'Error',
                'No ticket was found!'
            );

            $this->redirect()->toRoute(
                'ticket_sale_index'
            );

            return;
        }

        return $ticket;
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
            case 'orderid':
                return $this->getEntityManager()
                    ->getRepository('TicketBundle\Entity\Ticket')
                    ->findAllByEventAndOrderId($event, $this->getParam('string'));
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
        $url = str_replace('liv.', '', $url);

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
            ->setSubject($subject);

        if (getenv('APPLICATION_ENV') != 'development') {
            $this->getMailTransport()->send($mail);
        }
    }
}

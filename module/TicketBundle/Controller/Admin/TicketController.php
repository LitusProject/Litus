<?php

namespace TicketBundle\Controller\Admin;

use CommonBundle\Component\Util\File\TmpFile;
use CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile;
use DateTime;
use Laminas\Http\Headers;
use Laminas\View\Model\ViewModel;
use TicketBundle\Component\Document\Generator\Event\Csv as CsvGenerator;
use TicketBundle\Component\Document\Generator\Event\Pdf as PdfGenerator;
use TicketBundle\Entity\Event;

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
}

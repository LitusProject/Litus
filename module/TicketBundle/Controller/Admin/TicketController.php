<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace TicketBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Component\Util\File\TmpFile,
    CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile,
    DateTime,
    TicketBundle\Component\Document\Generator\Event as EventGenerator,
    Zend\Http\Headers,
    Zend\View\Model\ViewModel;

/**
 * TicketController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class TicketController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        if (!($event = $this->_getEvent()))
            return new ViewModel();

        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('TicketBundle\Entity\Ticket')
                ->findAllActiveByEvent($event),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'event' => $event,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function exportAction()
    {
        if (!($event = $this->_getEvent()))
            return new ViewModel();

        $file = new CsvFile();

        $language = $this->getLanguage();
        $file->appendContent(array('ID', 'Name', 'Status', 'Option', 'Number', 'Book Date', 'Sold Date', 'Member'));

        $tickets = $this->getEntityManager()
            ->getRepository('TicketBundle\Entity\Ticket')
            ->findAllActiveByEvent($event);

        $results = array();
        foreach ($tickets as $ticket) {
            $file->appendContent(
                array(
                    $ticket->getId(),
                    $ticket->getFullName(),
                    $ticket->getStatus(),
                    $ticket->getOption()->getName() . ' (' . ($ticket->isMember() ? 'Member' : 'Non Member') . ')',
                    $ticket->getNumber(),
                    $ticket->getBookDate() ? $ticket->getBookDate()->format('d/m/Y H:i') : '',
                    $ticket->getSoldDate() ? $ticket->getSoldDate()->format('d/m/Y H:i') : '',
                    $ticket->isMember() ? '1' : '0'
                )
            );
        }

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'attachment; filename="tickets.csv"',
            'Content-Type'        => 'text/csv',
        ));
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $file->getContent(),
            )
        );
    }

    public function printAction()
    {
        if (!($event = $this->_getEvent()))
            return new ViewModel();

        $file = new TmpFile();
        $document = new EventGenerator($this->getEntityManager(), $event, $file);
        $document->generate();

        $now = new DateTime();
        $filename = 'tickets_' . $now->format('Y_m_d') . '.pdf';

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Type'        => 'application/pdf',
        ));
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $file->getContent(),
            )
        );
    }

    private function _getEvent()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the event!'
                )
            );

            $this->redirect()->toRoute(
                'ticket_admin_event',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $event = $this->getEntityManager()
            ->getRepository('TicketBundle\Entity\Event')
            ->findOneById($this->getParam('id'));

        if (null === $event) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No event with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'ticket_admin_event',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $event;
    }

    private function _getTicket()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the ticket!'
                )
            );

            $this->redirect()->toRoute(
                'ticket_admin_event',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $ticket = $this->getEntityManager()
            ->getRepository('TicketBundle\Entity\Ticket')
            ->findOneById($this->getParam('id'));

        if (null === $ticket) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ticket with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'ticket_admin_event',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $ticket;
    }
}

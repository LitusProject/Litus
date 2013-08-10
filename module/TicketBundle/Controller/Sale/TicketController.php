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

namespace TicketBundle\Controller\Sale;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    Zend\View\Model\ViewModel;

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
                ->findAllByEvent($event),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function unassignAction()
    {
        $this->initAjax();

        if (!($ticket = $this->_getTicket()))
            return new ViewModel();

        $ticket->setStatus('empty');
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array("status" => "success"),
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($ticket = $this->_getTicket()) && !$ticket->getEvent()->areTicketsGenerated())
            return new ViewModel();

        $this->getEntityManager()->remove($ticket);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array("status" => "success"),
            )
        );
    }

    public function saleAction()
    {
        $this->initAjax();

        if (!($ticket = $this->_getTicket()))
            return new ViewModel();

        $ticket->setStatus('sold');
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array("status" => "success"),
            )
        );
    }

    public function undoSaleAction()
    {
        $this->initAjax();

        if (!($ticket = $this->_getTicket()))
            return new ViewModel();

        $ticket->setStatus('booked');
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array("status" => "success"),
            )
        );
    }

    private function _getTicket()
    {
        if (null === $this->getParam('ticket')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the ticket!'
                )
            );

            $this->redirect()->toRoute(
                'ticket_sale_index'
            );

            return;
        }

        $ticket = $this->getEntityManager()
            ->getRepository('TicketBundle\Entity\Ticket')
            ->findOneById($this->getParam('ticket'));

        if (null === $ticket) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ticket with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'ticket_sale_index'
            );

            return;
        }

        return $ticket;
    }
}
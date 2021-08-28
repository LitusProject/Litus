<?php

namespace TicketBundle\Controller\Sale;

use Laminas\View\Model\ViewModel;
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

        if (!$ticket->getEvent()->areTicketsGenerated()) {
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

    /**
     * @return Ticket|null
     */
    private function getTicketEntity()
    {
        $ticket = $this->getEntityById('TicketBundle\Entity\Ticket');

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
}

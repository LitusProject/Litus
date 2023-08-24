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
            $ticket->sendQrMail($this, $this->getLanguage());
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
            $item->payId = $ticket->getPayId();
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
            case 'orderid':
                return $this->getEntityManager()
                    ->getRepository('TicketBundle\Entity\Ticket')
                    ->findAllByEventAndOrderId($event, $this->getParam('string'));
            case 'payid':
                return $this->getEntityManager()
                    ->getRepository('TicketBundle\Entity\Ticket')
                    ->findAllByEventAndPayId($event, $this->getParam('string'));
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

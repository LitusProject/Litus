<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace TicketBundle\Controller\Sale;

use TicketBundle\Entity\Ticket,
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

        if (!($ticket = $this->getTicketEntity())) {
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

        if (!($ticket = $this->getTicketEntity()) || !$ticket->getEvent()->areTicketsGenerated()) {
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

        if (!($ticket = $this->getTicketEntity())) {
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

        if (!($ticket = $this->getTicketEntity())) {
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

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
 *
 * @license http://litus.cc/LICENSE
 */

namespace TicketBundle\Controller;

use TicketBundle\Component\Ticket\Ticket as TicketBook,
    TicketBundle\Entity\Event,
    TicketBundle\Entity\Ticket,
    Zend\View\Model\ViewModel;

/**
 * TicketController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class TicketController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function eventAction()
    {
        if (!($event = $this->getEventEntity())) {
            return $this->notFoundAction();
        }

        if (!($person = $this->getPersonEntity())) {
            return $this->notFoundAction();
        }

        $tickets = $this->getEntityManager()
            ->getRepository('TicketBundle\Entity\Ticket')
            ->findAllByEventAndPerson($event, $person);

        $form = $this->getForm('ticket_ticket_book', array('event' => $event, 'person' => $person));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                $numbers = array(
                    'member' => isset($formData['number_member']) ? $formData['number_member'] : 0,
                    'non_member' => isset($formData['number_non_member']) ? $formData['number_non_member'] : 0,
                );

                foreach ($event->getOptions() as $option) {
                    $numbers['option_' . $option->getId() . '_number_member'] = $formData['option_' . $option->getId() . '_number_member'];
                    $numbers['option_' . $option->getId() . '_number_non_member'] = $formData['option_' . $option->getId() . '_number_non_member'];
                }

                TicketBook::book(
                    $event,
                    $person,
                    null,
                    $numbers,
                    false,
                    $this->getEntityManager()
                );

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The tickets were succesfully booked'
                );

                $this->redirect()->toRoute(
                    'ticket',
                    array(
                        'action' => 'event',
                        'id' => $event->getId(),
                    )
                );
            }
        }

        $organizationStatus = $person->getOrganizationStatus($this->getCurrentAcademicYear());

        return new ViewModel(
            array(
                'event' => $event,
                'tickets' => $tickets,
                'form' => $form,
                'canRemoveReservations' => $event->canRemoveReservation($this->getEntityManager()),
                'isPraesidium' => $organizationStatus ? $organizationStatus->getStatus() == 'praesidium' : false,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($ticket = $this->getTicketEntity())) {
            return $this->notFoundAction();
        }

        if ($ticket->getEvent()->areTicketsGenerated()) {
            $ticket->setStatus('empty');
        } else {
            $this->getEntityManager()->remove($ticket);
        }

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return \CommonBundle\Entity\User\Person|null
     */
    private function getPersonEntity()
    {
        if (!$this->getAuthentication()->isAuthenticated()) {
            return;
        }

        return $this->getAuthentication()->getPersonObject();
    }

    /**
     * @return Event|null
     */
    private function getEventEntity()
    {
        $event = $this->getEntityById('TicketBundle\Entity\Event');

        if (!($event instanceof Event) || !$event->isActive()) {
            return;
        }

        return $event;
    }

    /**
     * @return Ticket|null
     */
    private function getTicketEntity()
    {
        if (!($person = $this->getPersonEntity())) {
            return;
        }

        $ticket = $this->getEntityById('TicketBundle\Entity\Ticket');

        if (!($ticket instanceof Ticket) || $ticket->getPerson() != $person) {
            return;
        }

        return $ticket;
    }
}

<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace TicketBundle\Controller;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Entity\User\Person,
    TicketBundle\Entity\Event,
    TicketBundle\Entity\Option,
    TicketBundle\Entity\Ticket,
    TicketBundle\Form\Ticket\Book as BookForm,
    Zend\View\Model\ViewModel;

/**
 * TicketController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class TicketController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function eventAction ()
    {
        if (!($event = $this->_getEvent()))
            return new ViewModel();

        $tickets = $this->getEntityManager()
            ->getRepository('TicketBundle\Entity\Ticket')
            ->findAllByEventAndPerson($event, $this->getAuthentication()->getPersonObject());

        $form = new BookForm($this->getEntityManager(), $event, $this->getAuthentication()->getPersonObject());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $numberTickets = 0;
                if (count($event->getOptions()) == 0) {
                    $numberTickets += $formData['number_member'];
                    if (!$event->isOnlyMembers())
                        $numberTickets += $formData['number_non_member'];
                } else {
                    foreach($event->getOptions() as $option) {
                        $numberTickets += $formData['options_' . $option->getId() . '_number_member'];
                        if (!$event->isOnlyMembers())
                            $numberTickets += $formData['options_' . $option->getId() . '_number_non_member'];
                    }
                }

                $person = $this->getAuthentication()->getPersonObject();

                if ($numberTickets > $event->getNumberFree() || $event->getNumberOfTickets() == 0) {
                    if ($event->areTicketsGenerated()) {
                        $tickets = $this->getEntityManager()
                            ->getRepository('TicketBundle\Entity\Ticket')
                            ->findAllEmptyByEvent($event);

                        if (count($event->getOptions()) == 0) {
                            $number = $formData['number_member'];
                            for($i = 0 ; $i < count($tickets) ; $i++) {
                                if (0 == $number)
                                    break;

                                $number--;
                                $tickets[$i]->setPerson($person)
                                    ->setMember(true)
                                    ->setStatus('booked');
                            }

                            if (!$event->isOnlyMembers()) {
                                $number = $formData['number_non_member'];
                                for(; $i < count($tickets) ; $i++) {
                                    if (0 == $number)
                                        break;

                                    $number--;
                                    $tickets[$i]->setPerson($person)
                                        ->setMember(false)
                                        ->setStatus('booked');
                                }
                            }
                        } else {
                            foreach($event->getOptions() as $option) {
                                $number = $formData['option_' . $option->getId() . '_number_member'];
                                for($i = 0; $i < count($tickets) ; $i++) {
                                    if (0 == $number)
                                        break;

                                    $number--;
                                    $tickets[$i]->setPerson($person)
                                        ->setMember(true)
                                        ->setOption($option)
                                        ->setStatus('booked');
                                }

                                if (!$event->isOnlyMembers()) {
                                    $number = $formData['option_' . $option->getId() . '_number_non_member'];
                                    for(; $i < count($tickets) ; $i++) {
                                        if (0 == $number)
                                            break;

                                        $number--;
                                        $tickets[$i]->setPerson($person)
                                            ->setMember(false)
                                            ->setOption($option)
                                            ->setStatus('booked');
                                    }
                                }
                            }
                        }
                    } else {
                        if (count($event->getOptions()) == 0) {
                            for($i = 0 ; $i < $formData['number_member'] ; $i++) {
                                $this->getEntityManager()->persist(
                                    $this->_createTicket($event, $person, true)
                                );
                            }

                            if (!$event->isOnlyMembers()) {
                                for($i = 0 ; $i < $formData['number_non_member'] ; $i++) {
                                    $this->getEntityManager()->persist(
                                        $this->_createTicket($event, $person, false)
                                    );
                                }
                            }
                        } else {
                            foreach($event->getOptions() as $option) {
                                for($i = 0 ; $i < $formData['option_' . $option->getId() . '_number_member'] ; $i++) {
                                    $this->getEntityManager()->persist(
                                        $this->_createTicket($event, $person, true, $option)
                                    );
                                }

                                if (!$event->isOnlyMembers()) {
                                    for($i = 0 ; $i < $formData['option_' . $option->getId() . '_number_non_member'] ; $i++) {
                                        $this->getEntityManager()->persist(
                                            $this->_createTicket($event, $person, false, $option)
                                        );
                                    }
                                }
                            }
                        }
                    }

                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::SUCCESS,
                            'Error',
                            'The tickets were succesfully booked'
                        )
                    );
                } else {
                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::ERROR,
                            'Error',
                            'There were not enough tickets available'
                        )
                    );
                }

                $this->redirect()->toRoute(
                    'ticket',
                    array(
                        'action' => 'event',
                        'id' => $event->getId(),
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'event' => $event,
                'tickets' => $tickets,
                'form' => $form,
            )
        );
    }

    private function _createTicket(Event $event, Person $person, $member, Option $option = null)
    {
        $ticket = new Ticket(
            $event,
            'empty',
            $person,
            null,
            null,
            $event->generateTicketNumber($this->getEntityManager())
        );
        $ticket->setMember($member)
            ->setStatus('booked')
            ->setOption($option);

        return $ticket;
    }

    private function _getEvent()
    {
        if (null === $this->getParam('id')) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $event = $this->getEntityManager()
            ->getRepository('TicketBundle\Entity\Event')
            ->findOneById($this->getParam('id'));

        if (null == $event) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        return $event;
    }
}
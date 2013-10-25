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
    CommonBundle\Entity\User\Person,
    TicketBundle\Entity\Event,
    TicketBundle\Entity\Option,
    TicketBundle\Entity\Ticket,
    TicketBundle\Form\Sale\Ticket\Add as AddForm,
    Zend\View\Model\ViewModel;

/**
 * IndexController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class IndexController extends \TicketBundle\Component\Controller\SaleController
{
    public function saleAction()
    {
        $event = $this->getEntityManager()
            ->getRepository('TicketBundle\Entity\Event')
            ->findOneById($this->getParam('id'));

        $form = new AddForm($this->getEntityManager(), $event);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $person = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Academic')
                    ->findOneById($formData['person_id']);

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
                                ->setStatus($formData['payed'] ? 'sold' : 'booked');
                        }

                        if (!$event->isOnlyMembers()) {
                            $number = $formData['number_non_member'];
                            for(; $i < count($tickets) ; $i++) {
                                if (0 == $number)
                                    break;

                                $number--;
                                $tickets[$i]->setPerson($person)
                                    ->setMember(false)
                                    ->setStatus($formData['payed'] ? 'sold' : 'booked');
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
                                    ->setStatus($formData['payed'] ? 'sold' : 'booked');
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
                                        ->setStatus($formData['payed'] ? 'sold' : 'booked');
                                }
                            }
                        }
                    }
                } else {
                    if (count($event->getOptions()) == 0) {
                        for($i = 0 ; $i < $formData['number_member'] ; $i++) {
                            $this->getEntityManager()->persist(
                                $this->_createTicket($event, $person, true, $formData['payed'])
                            );
                        }

                        if (!$event->isOnlyMembers()) {
                            for($i = 0 ; $i < $formData['number_non_member'] ; $i++) {
                                $this->getEntityManager()->persist(
                                    $this->_createTicket($event, $person, false, $formData['payed'])
                                );
                            }
                        }
                    } else {
                        foreach($event->getOptions() as $option) {
                            for($i = 0 ; $i < $formData['option_' . $option->getId() . '_number_member'] ; $i++) {
                                $this->getEntityManager()->persist(
                                    $this->_createTicket($event, $person, true, $formData['payed'], $option)
                                );
                            }

                            if (!$event->isOnlyMembers()) {
                                for($i = 0 ; $i < $formData['option_' . $option->getId() . '_number_non_member'] ; $i++) {
                                    $this->getEntityManager()->persist(
                                        $this->_createTicket($event, $person, false, $formData['payed'], $option)
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
                        'The tickets were succesfully ' . ($formData['payed'] ? 'sold' : 'booked')
                    )
                );

                $this->redirect()->toRoute(
                    'ticket_sale_index',
                    array(
                        'action' => 'sale',
                        'id' => $event->getId(),
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

    public function validateAction()
    {
        $event = $this->getEntityManager()
            ->getRepository('TicketBundle\Entity\Event')
            ->findOneById($this->getParam('id'));

        $form = new AddForm($this->getEntityManager(), $event);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                return new ViewModel(
                    array(
                        'status' => 'success',
                        'info' => array('status' => 'success'),
                    )
                );
            } else {
                $errors = $form->getMessages();
                $formErrors = array();

                foreach ($form->getElements() as $key => $element) {
                    if (!isset($errors[$element->getName()]))
                        continue;

                    $formErrors[$element->getAttribute('id')] = array();

                    foreach ($errors[$element->getName()] as $error) {
                        $formErrors[$element->getAttribute('id')][] = $error;
                    }
                }

                return new ViewModel(
                    array(
                        'status' => 'error',
                        'form' => array(
                            'errors' => $formErrors
                        ),
                    )
                );
            }
        }

        return new ViewModel();
    }

    private function _createTicket(Event $event, Person $person, $member, $payed, Option $option = null)
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
            ->setStatus($payed ? 'sold' : 'booked')
            ->setOption($option);

        return $ticket;
    }
}
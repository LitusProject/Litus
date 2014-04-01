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

namespace TicketBundle\Controller\Sale;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Entity\User\Person,
    TicketBundle\Entity\Event,
    TicketBundle\Entity\GuestInfo,
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

                if ($formData['is_guest']) {
                    $person = null;
                    $guestInfo = new GuestInfo($formData['guest_first_name'], $formData['guest_last_name'], $formData['guest_email']);
                    $this->getEntityManager()->persist($guestInfo);
                } else {
                    $person = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\User\Person\Academic')
                        ->findOneById($formData['person_id']);
                    $guestInfo = null;
                }

                if ($event->areTicketsGenerated()) {
                    $tickets = $this->getEntityManager()
                        ->getRepository('TicketBundle\Entity\Ticket')
                        ->findAllEmptyByEvent($event);

                    if (count($event->getOptions()) == 0) {
                        $number = $formData['number_member'];
                        $nbTickets = count($tickets);
                        for ($i = 0 ; $i < $nbTickets ; $i++) {
                            if (0 == $number)
                                break;

                            $number--;
                            $tickets[$i]->setPerson($person)
                                ->setGuestInfo($guestInfo)
                                ->setMember(true)
                                ->setStatus($formData['payed'] ? 'sold' : 'booked');
                        }

                        if (!$event->isOnlyMembers()) {
                            $number = $formData['number_non_member'];
                            $nbTickets = count($tickets);
                            for (; $i < $nbTickets ; $i++) {
                                if (0 == $number)
                                    break;

                                $number--;
                                $tickets[$i]->setPerson($person)
                                    ->setGuestInfo($guestInfo)
                                    ->setMember(false)
                                    ->setStatus($formData['payed'] ? 'sold' : 'booked');
                            }
                        }
                    } else {
                        foreach ($event->getOptions() as $option) {
                            $number = $formData['option_' . $option->getId() . '_number_member'];
                            $nbTickets = count($tickets);
                            for ($i = 0 ; $i < $nbTickets ; $i++) {
                                if (0 == $number)
                                    break;

                                $number--;
                                $tickets[$i]->setPerson($person)
                                    ->setGuestInfo($guestInfo)
                                    ->setMember(true)
                                    ->setOption($option)
                                    ->setStatus($formData['payed'] ? 'sold' : 'booked');
                            }

                            if (!$event->isOnlyMembers()) {
                                $number = $formData['option_' . $option->getId() . '_number_non_member'];
                                $nbTickets = count($tickets);
                                for (; $i < $nbTickets ; $i++) {
                                    if (0 == $number)
                                        break;

                                    $number--;
                                    $tickets[$i]->setPerson($person)
                                        ->setGuestInfo($guestInfo)
                                        ->setMember(false)
                                        ->setOption($option)
                                        ->setStatus($formData['payed'] ? 'sold' : 'booked');
                                }
                            }
                        }
                    }
                } else {
                    if (count($event->getOptions()) == 0) {
                        for ($i = 0 ; $i < $formData['number_member'] ; $i++) {
                            $this->getEntityManager()->persist(
                                $this->_createTicket($event, $person, $guestInfo, true, $formData['payed'])
                            );
                        }

                        if (!$event->isOnlyMembers()) {
                            for ($i = 0 ; $i < $formData['number_non_member'] ; $i++) {
                                $this->getEntityManager()->persist(
                                    $this->_createTicket($event, $person, $guestInfo, false, $formData['payed'])
                                );
                            }
                        }
                    } else {
                        foreach ($event->getOptions() as $option) {
                            $nbMember = $formData['option_' . $option->getId() . '_number_member'];
                            for ($i = 0 ; $i < $nbMember ; $i++) {
                                $this->getEntityManager()->persist(
                                    $this->_createTicket($event, $person, $guestInfo, true, $formData['payed'], $option)
                                );
                            }

                            if (!$event->isOnlyMembers()) {
                                $nbNonMember = $formData['option_' . $option->getId() . '_number_non_member'];
                                for ($i = 0 ; $i < $nbNonMember ; $i++) {
                                    $this->getEntityManager()->persist(
                                        $this->_createTicket($event, $person, $guestInfo, false, $formData['payed'], $option)
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

                foreach ($form->getElements() as $element) {
                    if (!isset($errors[$element->getName()]))
                        continue;

                    $formErrors[$element->getAttribute('id')] = array();

                    foreach ($errors[$element->getName()] as $error) {
                        $formErrors[$element->getAttribute('id')][] = $error;
                    }
                }

                foreach ($form->getFieldSets() as $fieldset) {
                    foreach ($fieldset->getElements() as $subElement) {
                        if (!isset($errors[$fieldset->getName()][$subElement->getName()]))
                            continue;

                        $formErrors[$subElement->getAttribute('id')] = array();

                        foreach ($errors[$fieldset->getName()][$subElement->getName()] as $error) {
                            $formErrors[$subElement->getAttribute('id')][] = $error;
                        }
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

    /**
     * @param boolean $member
     */
    private function _createTicket(Event $event, Person $person = null, GuestInfo $guestInfo = null, $member, $payed, Option $option = null)
    {
        $ticket = new Ticket(
            $event,
            'empty',
            $person,
            $guestInfo,
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

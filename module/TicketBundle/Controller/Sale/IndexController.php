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

        $form = new AddForm($event);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $person = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\Users\People\Academic')
                    ->findOneById($formData['person_id']);

                $notEnoughTickets = false;

                if ($event->areTicketsGenerated()) {
                    $tickets = $this->getEntityManager()
                        ->getRepository('TicketBundle\Entity\Ticket')
                        ->findAllEmptyByEvent($event);

                    if ($event->getNumberFree() > $formData['number_member'] + $formData['number_non_member']) {
                        $number = $formData['number_member'];
                        for($i = 0 ; $i < count($tickets) ; $i++) {
                            if (0 == $number)
                                break;

                            $number--;
                            $tickets[$i]->setPerson($person)
                                ->setMember(true)
                                ->setStatus($formData['payed'] ? 'sold' : 'booked');
                        }

                        $number = $formData['number_non_member'];
                        for(; $i < count($tickets) ; $i++) {
                            if (0 == $number)
                                break;

                            $number--;
                            $tickets[$i]->setPerson($person)
                                ->setMember(false)
                                ->setStatus($formData['payed'] ? 'sold' : 'booked');
                        }
                    } else {
                        $notEnoughTickets = true;
                    }
                } else {
                    if ($event->getNumberFree() > $formData['number_member'] + $formData['number_non_member']) {
                        for($i = 0 ; $i < $formData['number_member'] ; $i++) {
                            $ticket = new Ticket(
                                $event,
                                'empty',
                                $person,
                                null,
                                null,
                                $event->generateTicketNumber($this->getEntityManager())
                            );
                            $ticket->setMember(true)
                                ->setStatus($formData['payed'] ? 'sold' : 'booked');
                            $this->getEntityManager()->persist($ticket);
                        }

                        for($i = 0 ; $i < $formData['number_non_member'] ; $i++) {
                            $ticket = new Ticket(
                                $event,
                                'empty',
                                $person,
                                null,
                                null,
                                $event->generateTicketNumber($this->getEntityManager())
                            );
                            $ticket->setMember(false)
                                ->setStatus($formData['payed'] ? 'sold' : 'booked');
                            $this->getEntityManager()->persist($ticket);
                        }
                    } else {
                        $notEnoughTickets = true;
                    }
                }

                if ($notEnoughTickets) {
                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::ERROR,
                            'Error',
                            'There were not enough tickets available'
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
                } else {
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
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }
}
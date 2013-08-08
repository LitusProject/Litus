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
    DateTime,
    TicketBundle\Entity\Event,
    TicketBundle\Entity\Option,
    TicketBundle\Entity\Ticket,
    TicketBundle\Form\Admin\Event\Add as AddForm,
    TicketBundle\Form\Admin\Event\Edit as EditForm,
    Zend\View\Model\ViewModel;

/**
 * EventController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class EventController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('TicketBundle\Entity\Event')
                ->findAll(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function oldAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('TicketBundle\Entity\Event')
                ->findOld(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = new AddForm($this->getEntityManager());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $event = new Event(
                    $this->getEntityManager()
                        ->getRepository('CalendarBundle\Entity\Node\Event')
                        ->findOneById($formData['event']),
                    $formData['bookable'],
                    strlen($formData['bookings_close_date']) ? DateTime::createFromFormat('d#m#Y H#i', $formData['bookings_close_date']) : null,
                    $formData['active'],
                    $formData['generate_tickets'],
                    $formData['number_of_tickets'],
                    $formData['limit_per_person'],
                    $formData['only_members'],
                    $formData['enable_options'] ? 0 : $formData['price_members'],
                    $formData['enable_options'] && !$formData['only_members'] ? 0 : $formData['price_non_members']
                );

                foreach($formData['options'] as $option) {
                    if (strlen($option['option']) == 0)
                        break;
                    $option = new Option($event, $option['option'], $option['price_members'], !$formData['only_members'] ? 0 : $option['price_non_members']);
                    $this->getEntityManager()->persist($option);
                }

                if ($formData['generate_tickets']) {
                    for($i = 0 ; $i < $formData['number_of_tickets'] ; $i++) {
                        $ticket = new Ticket($event, 'empty', null, null, null, $event->generateTicketNumber($this->getEntityManager()));
                        $this->getEntityManager()->persist($ticket);
                        $this->getEntityManager()->flush();
                    }
                }

                $this->getEntityManager()->persist($event);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The event was successfully created!'
                    )
                );

                $this->redirect()->toRoute(
                    'ticket_admin_event',
                    array(
                        'action' => 'manage'
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

    public function editAction()
    {
        if (!($event = $this->_getEvent()))
            return new ViewModel();

        $form = new EditForm($event, $this->getEntityManager());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                if ($formData['generate_tickets']) {
                    if ($event->areTicketsGenerated()) {
                        if ($formData['number_of_tickets'] >= $event->getNumberOfTickets()) {
                            for($i = $event->getNumberOfTickets() ; $i < $formData['number_of_tickets'] ; $i++) {
                                do {
                                    $number = rand();
                                    $ticket = $this->getEntityManager()
                                        ->getRepository('TicketBundle\Entity\Ticket')
                                        ->findOneByEventAndNumber($event, $number);
                                } while($ticket !== null);

                                $ticket = new Ticket($event, 'empty', null, null, null, $number);
                                $this->getEntityManager()->persist($ticket);
                                $this->getEntityManager()->flush();
                            }
                        } else {
                            $tickets = $this->getEntityManager()
                                ->getRepository('TicketBundle\Entity\Ticket')
                                ->findAllEmptyByEvent($event);
                            $numberOfTickets = $event->getNumberOfTickets() - $formData['number_of_tickets'];

                            foreach($tickets as $ticket) {
                                if ($numberOfTickets == 0)
                                    break;

                                $numberOfTickets--;
                                $this->getEntityManager()->remove($ticket);
                            }
                        }
                    } else {
                        for($i = 0 ; $i < $formData['number_of_tickets'] ; $i++) {
                            do {
                                $number = rand();
                                $ticket = $this->getEntityManager()
                                    ->getRepository('TicketBundle\Entity\Ticket')
                                    ->findOneByEventAndNumber($event, $number);
                            } while($ticket !== null);

                            $ticket = new Ticket($event, 'empty', null, null, null, $number);
                            $this->getEntityManager()->persist($ticket);
                            $this->getEntityManager()->flush();
                        }
                    }
                } else {
                    $tickets = $this->getEntityManager()
                        ->getRepository('TicketBundle\Entity\Ticket')
                        ->findAllEmptyByEvent($event);
                    foreach($tickets as $ticket) {
                        $this->getEntityManager()->remove($ticket);
                    }
                }

                $event->setActivity($this->getEntityManager()
                        ->getRepository('CalendarBundle\Entity\Node\Event')
                        ->findOneById($formData['event']))
                    ->setBookable($formData['bookable'])
                    ->setBookingsCloseDate(strlen($formData['bookings_close_date']) ? DateTime::createFromFormat('d#m#Y H#i', $formData['bookings_close_date']) : null)
                    ->setActive($formData['active'])
                    ->setTicketsGenerated($formData['generate_tickets'])
                    ->setNumberOfTickets($formData['number_of_tickets'])
                    ->setLimitPerPerson($formData['limit_per_person'])
                    ->setOnlyMembers($formData['only_members'])
                    ->setPriceMembers($formData['price_members'])
                    ->setPriceNonMembers($formData['price_non_members']);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The event was successfully updated!'
                    )
                );

                $this->redirect()->toRoute(
                    'ticket_admin_event',
                    array(
                        'action' => 'manage'
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'event' => $event,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($event = $this->_getEvent()))
            return new ViewModel();

        $event->setActive(false);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array("status" => "success"),
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
}

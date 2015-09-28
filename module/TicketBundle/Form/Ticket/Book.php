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

namespace TicketBundle\Form\Ticket;

use CommonBundle\Entity\User\Person;
use LogicException;
use RuntimeException;
use TicketBundle\Entity\Event;

/**
 * Book Tickets
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Book extends \CommonBundle\Component\Form\Bootstrap\Form
{
    /**
     * @var Event
     */
    private $event;

    /**
    * @var Person
    */
    private $person;

    public function init()
    {
        if (null === $this->event) {
            throw new LogicException('Cannot book ticket for null form.');
        }
        if (null === $this->person) {
            throw new RuntimeException('You have to be logged in to book tickets.');
        }

        parent::init();

        $this->setAttribute('id', 'ticket_sale_form');

        if ($this->event->getOptions()->isEmpty()) {
            $this->add(array(
                'type'       => 'select',
                'name'       => 'number_member',
                'label'      => 'Number Member',
                'attributes' => array(
                    'options' => $this->getNumberOptions(),
                ),
                'options'    => array(
                    'input' => array(
                        'required' => true,
                        'validators' => array(
                            array(
                                'name' => 'ticket_number_tickets',
                                'options' => array(
                                    'event' => $this->event,
                                    'person' => $this->person,
                                ),
                            ),
                        ),
                    ),
                ),
            ));

            if (!$this->event->isOnlyMembers()) {
                $this->add(array(
                    'type'       => 'select',
                    'name'       => 'number_non_member',
                    'label'      => 'Number Non Member',
                    'attributes' => array(
                        'options' => $this->getNumberOptions(),
                    ),
                    'options'    => array(
                        'input' => array(
                            'required' => true,
                            'validators' => array(
                                array(
                                    'name' => 'ticket_number_tickets',
                                    'options' => array(
                                        'event' => $this->event,
                                        'person' => $this->person,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ));
            }
        } else {
            foreach ($this->event->getOptions() as $option) {
                $this->add(array(
                    'type'       => 'select',
                    'name'       => 'option_' . $option->getId() . '_number_member',
                    'label'      => ucfirst($option->getName()) . ' (Member)',
                    'attributes' => array(
                        'options' => $this->getNumberOptions(),
                    ),
                    'options'    => array(
                        'input' => array(
                            'required' => true,
                            'validators' => array(
                                array(
                                    'name' => 'ticket_number_tickets',
                                    'options' => array(
                                        'event' => $this->event,
                                        'person' => $this->person,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ));

                if (!$this->event->isOnlyMembers()) {
                    $this->add(array(
                        'type'       => 'select',
                        'name'       => 'option_' . $option->getId() . '_number_non_member',
                        'label'      => ucfirst($option->getName()) . ' (Non Member)',
                        'attributes' => array(
                            'options' => $this->getNumberOptions(),
                        ),
                        'options'    => array(
                            'input' => array(
                                'required' => true,
                                'validators' => array(
                                    array(
                                        'name' => 'ticket_number_tickets',
                                        'options' => array(
                                            'event' => $this->event,
                                            'person' => $this->person,
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ));
                }
            }
        }

        $this->addSubmit('Book', 'book_tickets');
    }

    private function getNumberOptions()
    {
        $numbers = array();
        $max = $this->event->getLimitPerPerson() == 0 ? 10 : $this->event->getLimitPerPerson();

        for ($i = 0 ; $i <= $max ; $i++) {
            $numbers[$i] = $i;
        }

        return $numbers;
    }

    /**
     * @param  Event $event
     * @return self
     */
    public function setEvent(Event $event)
    {
        $this->event = $event;

        return $this;
    }

    /**
    * @param  Person $person
    * @return self
    */
    public function setPerson(Person $person)
    {
        $this->person = $person;

        return $this;
    }
}

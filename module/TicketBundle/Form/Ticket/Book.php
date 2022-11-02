<?php

namespace TicketBundle\Form\Ticket;

use CommonBundle\Entity\User\Person;
use LogicException;
use RuntimeException;
use TicketBundle\Entity\Event;
use Zend\Validator\Identical;

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

    /**
     * @var boolean Are the conditions already checked or not
     */
    protected $conditionsChecked = false;

    public function init()
    {
        if ($this->event === null) {
            throw new LogicException('Cannot book ticket for null form.');
        }
        if ($this->person === null) {
            throw new RuntimeException('You have to be logged in to book tickets.');
        }

        parent::init();

        $this->setAttribute('id', 'ticket_sale_form');

        if ($this->event->getOptions()->isEmpty()) {
            $this->add(
                array(
                    'type'       => 'select',
                    'name'       => 'number_member',
                    'label'      => 'Number Member',
                    'attributes' => array(
                        'options' => $this->getNumberOptions(),
                    ),
                    'options'    => array(
                        'input' => array(
                            'required'   => true,
                            'validators' => array(
                                array(
                                    'name'    => 'NumberTickets',
                                    'options' => array(
                                        'event'   => $this->event,
                                        'person'  => $this->person,
                                        'maximum' => $this->event->getLimitPerPerson(),
                                    ),
                                ),
                            ),
                        ),
                    ),
                )
            );

            if (!$this->event->isOnlyMembers()) {
                $this->add(
                    array(
                        'type'       => 'select',
                        'name'       => 'number_non_member',
                        'label'      => 'Number Non Member',
                        'attributes' => array(
                            'options' => $this->getNumberOptions(),
                        ),
                        'options'    => array(
                            'input' => array(
                                'required'   => true,
                                'validators' => array(
                                    array(
                                        'name'    => 'NumberTickets',
                                        'options' => array(
                                            'event'   => $this->event,
                                            'person'  => $this->person,
                                            'maximum' => $this->event->getLimitPerPerson(),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    )
                );
            }
        } else {
            foreach ($this->event->getOptions() as $option) {
                if ($option->isVisible()) {
                    $this->add(
                        array(
                            'type'       => 'select',
                            'name'       => 'option_' . $option->getId() . '_number_member',
                            'label'      => $option->getPriceNonMembers() != 0 ? ucfirst($option->getName()) . ' (Member)' : ucfirst($option->getName()),
                            'attributes' => array(
                                'options' => $this->getNumberOptions(),
                            ),
                            'options'    => array(
                                'input' => array(
                                    'required'   => true,
                                    'validators' => array(
                                        array(
                                            'name'    => 'NumberTickets',
                                            'options' => array(
                                                'event'   => $this->event,
                                                'person'  => $this->person,
                                                'maximum' => $this->event->getLimitPerPerson(),
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        )
                    );

                    if (!$this->event->isOnlyMembers() && $option->getPriceNonMembers() != 0) {
                        $this->add(
                            array(
                                'type' => 'select',
                                'name' => 'option_' . $option->getId() . '_number_non_member',
                                'label' => ucfirst($option->getName()) . ' (Non Member)',
                                'attributes' => array(
                                    'options' => $this->getNumberOptions(),
                                ),
                                'options' => array(
                                    'input' => array(
                                        'required' => true,
                                        'validators' => array(
                                            array(
                                                'name' => 'NumberTickets',
                                                'options' => array(
                                                    'event' => $this->event,
                                                    'person' => $this->person,
                                                    'maximum' => $this->event->getLimitPerPerson(),
                                                ),
                                            ),
                                        ),
                                    ),
                                ),
                            )
                        );
                    }
                }
            }
        }

        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'conditions',
                'label'      => 'I have read and accept the GDPR terms and condition specified above',
                'attributes' => array(
                    'id' => 'conditions',
                ),
                'options'    => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'identical',
                                'options' => array(
                                    'token'    => true,
                                    'strict'   => false,
                                    'messages' => array(
                                        Identical::NOT_SAME => 'You must agree to the terms and conditions.',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Book', 'book_tickets');
    }

    private function getNumberOptions()
    {
        $numbers = array();
        $max = $this->event->getLimitPerPerson() == 0 ? 10 : $this->event->getLimitPerPerson();

        for ($i = 0; $i <= $max; $i++) {
            $numbers[$i] = $i;
        }

        return $numbers;
    }

    /**
     * @param Event $event
     * @return self
     */
    public function setEvent(Event $event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @param Person $person
     * @return self
     */
    public function setPerson($person)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * @param boolean $conditionsChecked
     * @return self
     */
    public function setConditionsChecked($conditionsChecked = true)
    {
        $this->conditionsChecked = !!$conditionsChecked;

        return $this;
    }
}

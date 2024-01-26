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
                        //                                'options' => $this->getNumberOptions(),
                                'options' => $this->getLimitForOption($option),
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
                                'type'       => 'select',
                                'name'       => 'option_' . $option->getId() . '_number_non_member',
                                'label'      => ucfirst($option->getName()) . ' (Non Member)',
                                'attributes' => array(
                            //                                    'options' => $this->getNumberOptions(),
                                    'options' => $this->getLimitForOption($option),
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
                }
            }
        }

        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'conditions',
                'label'      => $this->getTermsLabel(),
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

    private function getLimitForOption(Event\Option $option)
    {
        $numbers = array();

        if ($option->getLimitPerPerson() == 0) {
            $max = $this->event->getLimitPerPerson() == 0 ? 10 : $this->event->getLimitPerPerson();
        } elseif ($option->getLimitPerPerson() > $this->event->getLimitPerPerson()) {
            $max = $this->event->getLimitPerPerson() == 0 ? 10 : $this->event->getLimitPerPerson();
        } else {
            $max = $option->getLimitPerPerson();
        }

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

    /**
     * @return string
     */
    private function getTermsLabel()
    {
        $urls = explode(',', $this->event->getTermsUrl());
        $text = $this->getServiceLocator()->get('translator')->translate('I have read and accept the terms and conditions specified');
        $here = $this->getServiceLocator()->get('translator')->translate('here');
        if (count($urls) == 1) {
            $text .= ' ' . str_replace(array('url', 'here'), array($urls[0], $here), '<a href="url" target="_blank"><strong><u>here</u></strong></a>.');
        } elseif (count($urls) > 1) {
            $text .= ' ' . str_replace(array('url', 'here'), array($urls[0], $here), '<a href="url" target="_blank"><strong><u>here</u></strong></a>');
            $length = count($urls);
            for ($i = 1; $i <= $length - 2; $i++) {
                $text .= ', ' . str_replace(array('url', 'here'), array($urls[$i], $here), '<a href="url" target="_blank"><strong><u>here</u></strong></a>');
            }
            $and = $this->getServiceLocator()->get('translator')->translate('and');
            $text .= ' ' . $and . ' ' . str_replace(array('url', 'here'), array(end($urls), $here), '<a href="url" target="_blank"><strong><u>here</u></strong></a>.');
        }
        return $text;
    }
}

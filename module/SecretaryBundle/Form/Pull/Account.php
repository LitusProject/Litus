<?php

namespace SecretaryBundle\Form\Pull;

use CommonBundle\Entity\User\Person;
use LogicException;
use RuntimeException;
use TicketBundle\Entity\Event;
Use Zend\Validator\Identical;

class Account extends \CommonBundle\Component\Form\Bootstrap\Form
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
        if ($this->event === null) {
            throw new LogicException('Cannot buy credits for null');
        }
        if ($this->person === null) {
            throw new RuntimeException('You have to be logged in to buy print Budget');
        }

        parent::init();

        $this->setAttribute('id', 'printer_buy_form');

        $this->add(
            array(
                'type' => 'fieldset',
                'name' => 'spacer',
                'label' => 'Print Budget',
                'elements' => array(// intentionally empty
                ),
            )
        );

        /**
         * To Do: Add Pull option menu
         */

        $this->add(
            array(
                'type' => 'checkbox',
                'name' => 'conditions',
                //                'label'      => 'I have read and accept the GDPR terms and condition specified above',
                'label' => 'Bij deze ga ik akkoord dat VTK mijn gegevens mag gebruiken voor de werking van de printer.',
                'attributes' => array(
                    'id' => 'conditions',
                ),
                'options' => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name' => 'identical',
                                'options' => array(
                                    'token' => true,
                                    'strict' => false,
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

        $this->addSubmit('Buy', 'buy_pull');
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

    public function setPerson($person)
    {
        $this->person = $person;

        return $this;
    }
}
<?php

namespace SecretaryBundle\Form\Pull;

use LogicException;
use TicketBundle\Entity\Event;
use TicketBundle\Entity\GuestInfo;
use Zend\Validator\Identical;

class Guest extends \CommonBundle\Component\Form\Bootstrap\Form
{
    /**
     * @var Event
     */
    private $event;

    /**
     * @var GuestInfo
     */
    private $guestInfo;

    public function init()
    {
        if ($this->event === null) {
            throw new LogicException('Cannot buy pull for null');
        }

        parent::init();

        $this->setAttribute('id', 'secretary_buy_pull');


        $this->add(
            array(
                'type'     => 'fieldset',
                'name'     => 'guest_form',
                'label'    => 'Contact Details',
                'elements' => array(
                    array(
                        'type'       => 'text',
                        'name'       => 'guest_first_name',
                        'label'      => 'First Name',
                        'required'   => true,
                        'attributes' => array(
                            'id' => 'guest_first_name',
                        ),
                        'options'    => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                            ),
                        ),
                    ),
                    array(
                        'type'       => 'text',
                        'name'       => 'guest_last_name',
                        'label'      => 'Last Name',
                        'required'   => true,
                        'attributes' => array(
                            'id' => 'guest_last_name',
                        ),
                        'options'    => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                            ),
                        ),
                    ),
                    array(
                        'type'       => 'text',
                        'name'       => 'guest_email',
                        'label'      => 'E-mail',
                        'required'   => true,
                        'attributes' => array(
                            'id' => 'guest_email',
                        ),
                        'options'    => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                                'validators' => array(
                                    array('name' => 'EmailAddress'),
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'fieldset',
                'name'     => 'spacer',
                'label'    => 'Department Pull',
                'elements' => array(
                    // intentionally empty
                ),
            )
        );

        /**
         * TO DO: Add Pull option selector
         */
        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'pull',
                'label'      => 'Option',
                'required'   => true,
                'attributes' => array(
                    'options' => $this->createPullArray(),
                ),
                'options'    => array(
                    'input' => array(
                        'filter' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'conditions',
                'label'      => 'I consent that my information will be used to help you further with your order.',
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

    protected function createPullArray()
    {
        $options = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Pull')
            ->findAllAvailableQuery()
            ->getResult();

        $pullArray = array(
            '' => '',
        );

        foreach ($options as $option) {
            $pullArray[$option->getId()] = $option->getStudyEn();
        }

        return $pullArray;
    }
}

<?php

namespace TicketBundle\Form\Sale\Ticket;

use TicketBundle\Entity\Event;

/**
 * Add Ticket
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{
    protected $hydrator = 'TicketBundle\Hydrator\Ticket';

    /**
     * @var Event
     */
    private $event;

    public function init()
    {
        parent::init();

        $this->setAttribute('id', 'ticket_sale_form');

        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'force',
                'label'      => 'Force (Ignore limits)',
                'required'   => false,
                'attributes' => array(
                    'id' => 'force',
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'is_guest',
                'label'      => 'Is Guest',
                'required'   => false,
                'attributes' => array(
                    'id' => 'is_guest',
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'fieldset',
                'name'     => 'person_form',
                'label'    => 'Person',
                'elements' => array(
                    array(
                        'type'     => 'typeahead',
                        'name'     => 'person',
                        'label'    => 'Person',
                        'required' => true,
                        'options'  => array(
                            'input' => array(
                                'validators' => array(
                                    array('name' => 'TypeaheadPerson'),
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
                'name'     => 'guest_form',
                'label'    => 'Guest',
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
                        'label'      => 'Email',
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
                    array(
                        'type'       => 'text',
                        'name'       => 'guest_organization',
                        'label'      => 'Organization',
                        'required'   => true,
                        'attributes' => array(
                            'id' => 'guest_organization',
                        ),
                        'options'    => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $optionsForm = $this->addFieldset('Options', 'options_form');

        if ($this->event->getOptions()->isEmpty()) {
            $optionsForm->add(
                array(
                    'type'       => 'select',
                    'name'       => 'number_member',
                    'label'      => 'Number Member',
                    'required'   => true,
                    'attributes' => array(
                        'class'      => 'ticket_option',
                        'id'         => 'number_member',
                        'data-price' => $this->event->getPriceMembers(),
                        'options'    => $this->getNumberOptions(),
                    ),
                    'options'    => array(
                        'input' => array(
                            'validators' => array(
                                array(
                                    'name'    => 'NumberTickets',
                                    'options' => array(
                                        'event' => $this->event,
                                    ),
                                ),
                            ),
                        ),
                    ),
                )
            );

            if (!$this->event->isOnlyMembers()) {
                $optionsForm->add(
                    array(
                        'type'       => 'select',
                        'name'       => 'number_non_member',
                        'label'      => 'Number Non Member',
                        'attributes' => array(
                            'class'      => 'ticket_option',
                            'id'         => 'number_non_member',
                            'data-price' => $this->event->getPriceNonMembers(),
                            'options'    => $this->getNumberOptions(),
                        ),
                        'options'    => array(
                            'input' => array(
                                'validators' => array(
                                    array(
                                        'name'    => 'NumberTickets',
                                        'options' => array(
                                            'event' => $this->event,
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
                $optionsForm->add(
                    array(
                        'type'       => 'select',
                        'name'       => 'option_' . $option->getId() . '_number_member',
                        'label'      => ucfirst($option->getName()) . ' (Member)',
                        'attributes' => array(
                            'class'      => 'ticket_option',
                            'id'         => 'option_' . $option->getId() . '_number_member',
                            'data-price' => $option->getPriceMembers(),
                            'options'    => $this->getNumberOptions(),
                        ),
                        'options'    => array(
                            'input' => array(
                                'validators' => array(
                                    array(
                                        'name'    => 'NumberTickets',
                                        'options' => array(
                                            'event' => $this->event,
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    )
                );

                if (!$this->event->isOnlyMembers()) {
                    $optionsForm->add(
                        array(
                            'type'       => 'select',
                            'name'       => 'option_' . $option->getId() . '_number_non_member',
                            'label'      => ucfirst($option->getName()) . ' (Non Member)',
                            'attributes' => array(
                                'class'      => 'ticket_option',
                                'id'         => 'option_' . $option->getId() . '_number_non_member',
                                'data-price' => $option->getPriceNonMembers(),
                                'options'    => $this->getNumberOptions(),
                            ),
                            'options'    => array(
                                'input' => array(
                                    'validators' => array(
                                        array(
                                            'name'    => 'NumberTickets',
                                            'options' => array(
                                                'event' => $this->event,
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

        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'payed',
                'label'      => 'Payed',
                'attributes' => array(
                    'id' => 'payed',
                ),
            )
        );

        $this->add(
            array(
                'type' => 'text',
                'name' => 'payId',
                'label' => 'Betaalreferentie',
                'required' => false,
                'attributes' => array(
                    'id' => 'payid',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'payId'),
                        ),
                    ),
                ),
            ),
        );

        $this->addSubmit('Sale', 'sale_tickets', 'sale', array('id' => 'sale_tickets'));
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

    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();

        $isGuest = isset($this->data['is_guest']) && $this->data['is_guest'];
        $force = isset($this->data['force']) && $this->data['force'];

        if ($isGuest) {
            unset($specs['person_form']);
        } else {
            unset($specs['guest_form']);
        }

        if ($force) {
            if ($this->event->getOptions()->isEmpty()) {
                foreach ($this->event->getOptions() as $option) {
                    unset($specs['options_form']['option_' . $option->getId() . '_number_member']);
                    unset($specs['options_form']['option_' . $option->getId() . '_number_non_member']);
                }
            } else {
                unset($specs['options_form']['number_member']);
                unset($specs['options_form']['number_non_member']);
            }
        }

        return $specs;
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
}

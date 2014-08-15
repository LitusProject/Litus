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

namespace TicketBundle\Form\Sale\Ticket;

use LogicException,
    TicketBundle\Component\Validator\NumberTickets as NumberTicketsValidator,
    TicketBundle\Entity\Event;

/**
 * Add Ticket
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{
    /**
     * @var Event
     */
    private $event;

    public function init()
    {
        if (null === $this->event) {
            throw new LogicException('Cannot create tickets for null event.');
        }

        parent::init();

        $this->setAttribute('id', 'ticket_sale_form');

        $this->add(array(
            'type'  => 'checkbox',
            'name'  => 'force',
            'label' => 'Force (Ignore limits)',
        ));

        $this->add(array(
            'type'  => 'checkbox',
            'name'  => 'is_guest',
            'label' => 'Is Guest',
        ));

        $this->add(array(
            'type'       => 'fieldset',
            'name'       => 'person_form',
            'label'      => 'Person',
            'attributes' => array(
                'id' => 'person_form'
            ),
            'elements'   => array(
                array(
                    'type'       => 'hidden',
                    'name'       => 'person_id',
                    'attributes' => array(
                        'id' => 'personId',
                    ),
                ),
                array(
                    'type'       => 'text',
                    'name'       => 'person',
                    'label'      => 'Person',
                    'required'   => true,
                    'attributes' => array(
                        'autocomplete' => 'off',
                        'data-provice' => 'typeahead',
                        'id'           => 'personSearch',
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'fieldset',
            'name'       => 'guest_form',
            'label'      => 'Guest',
            'attributes' => array(
                'id' => 'guest_form',
            ),
            'elements'   => array(
                array(
                    'type'     => 'text',
                    'name'     => 'guest_first_name',
                    'label'    => 'First Name',
                    'required' => true,
                ),
                array(
                    'type'     => 'text',
                    'name'     => 'guest_last_name',
                    'label'    => 'Last Name',
                    'required' => true,
                ),
                array(
                    'type'     => 'text',
                    'name'     => 'guest_email',
                    'label'    => 'Email',
                    'required' => true,
                ),
            ),
        ));

        $optionElements = array();

        if (empty($this->event->getOptions())) {
            $optionElements[] = array(
                'type'       => 'select',
                'name'       => 'number_member',
                'label'      => 'Number Member',
                'attributes' => array(
                    'class'      => 'ticket_option',
                    'data-price' => $this->event->getPriceMembers(),
                    'options'    => $this->getNumberOptions(),
                ),
            );

            if (!$this->event->isOnlyMembers()) {
                $optionElements[] = array(
                    'type'       => 'select',
                    'name'       => 'number_non_member',
                    'label'      => 'Number Non Member',
                    'attributes' => array(
                        'class'      => 'ticket_option',
                        'data-price' => $this->event->getPriceNonMembers(),
                        'options'    => $this->getNumberOptions(),
                    ),
                );
            }
        } else {
            foreach ($this->event->getOptions() as $option) {
                $optionElements[] = array(
                    'type'       => 'select',
                    'name'       => 'option_' . $option->getId() . '_number_member',
                    'label'      => ucfirst($option->getName()) . ' (Member)',
                    'attributes' => array(
                        'class'      => 'ticket_option',
                        'data-price' => $option->getPriceMembers(),
                        'options'    => $this->getNumberOptions(),
                    ),
                );

                if (!$this->event->isOnlyMembers()) {
                    $optionElements[] = array(
                        'type'       => 'select',
                        'name'       => 'option_' . $option->getId() . '_number_non_member',
                        'label'      => ucfirst($option->getName()) . ' (Non Member)',
                        'attributes' => array(
                            'class'      => 'ticket_option',
                            'data-price' => $option->getPriceNonMembers(),
                            'options'    => $this->getNumberOptions(),
                        ),
                    );
                }
            }
        }

        $this->add(array(
            'type'     => 'fieldset',
            'name'     => 'options',
            'label'    => 'Options',
            'elements' => $optionElements,
        ));

        $this->add(array(
            'type'  => 'checkbox',
            'name'  => 'payed',
            'label' => 'Payed',
        ));

        $this->addSubmit('Sale', 'sale_tickets');
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

    public function getInputFilterSpecification()
    {
        $inputFilter = array();

        $inputFilter[] = array(
            'name'     => 'force',
            'required' => false,
        );

        $inputFilter[] = array(
            'name'     => 'is_guest',
            'required' => false,
        );

        $isGuest = isset($this->data['is_guest']) && $this->data['is_guest'];
        $force = isset($this->data['force']) && $this->data['force'];

        $inputFilter[] = array(
            'name'     => 'guest_first_name',
            'required' => $isGuest,
            'filters'  => array(
                array('name' => 'StringTrim'),
            ),
        );

        $inputFilter[] = array(
            'name'     => 'guest_last_name',
            'required' => $isGuest,
            'filters'  => array(
                array('name' => 'StringTrim'),
            ),
        );

        $inputFilter[] = array(
            'name'     => 'guest_email',
            'required' => $isGuest,
            'filters'  => array(
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array('name' => 'EmailAddress'),
            ),
        );

        $inputFilter[] = array(
            'name'     => 'person_id',
            'required' => !$isGuest,
            'filters'  => array(
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'int',
                ),
            ),
        );

        $inputFilter[] = array(
            'name'     => 'person',
            'required' => !$isGuest,
            'filters'  => array(
                array('name' => 'StringTrim'),
            ),
        );

        if (!$force) {
            if (empty($this->event->getOptions())) {
                $inputFilter[] = array(
                    'name'     => 'number_member',
                    'required' => true,
                    'validators' => array(
                        new NumberTicketsValidator($this->getEntityManager(), $this->event),
                    ),
                );

                if (!$this->event->isOnlyMembers()) {
                    $inputFilter[] = array(
                        'name'     => 'number_non_member',
                        'required' => true,
                        'validators' => array(
                            new NumberTicketsValidator($this->getEntityManager(), $this->event),
                        ),
                    );
                }
            } else {
                $options = array();
                foreach ($this->event->getOptions() as $option) {
                    $options[] = array(
                        'name'     => 'option_' . $option->getId() . '_number_member',
                        'required' => true,
                        'validators' => array(
                            new NumberTicketsValidator($this->getEntityManager(), $this->event),
                        ),
                    );

                    if (!$this->event->isOnlyMembers()) {
                        $options[] = array(
                            'name'     => 'option_' . $option->getId() . '_number_non_member',
                            'required' => true,
                            'validators' => array(
                                new NumberTicketsValidator($this->getEntityManager(), $this->event),
                            ),
                        );
                    }
                }

                $inputFilter['options'] = $options;
            }
        }

        return $inputFilter;
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

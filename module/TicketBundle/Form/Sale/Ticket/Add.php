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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace TicketBundle\Form\Sale\Ticket;

use LogicException,
    TicketBundle\Entity\Event;

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
        if (null === $this->event) {
            throw new LogicException('Cannot create tickets for null event.');
        }

        parent::init();

        $this->setAttribute('id', 'ticket_sale_form');

        $this->add(array(
            'type'       => 'checkbox',
            'name'       => 'force',
            'label'      => 'Force (Ignore limits)',
            'required'   => false,
            'attributes' => array(
                'id' => 'force',
            ),
        ));

        $this->add(array(
            'type'       => 'checkbox',
            'name'       => 'is_guest',
            'label'      => 'Is Guest',
            'required'   => false,
            'attributes' => array(
                'id' => 'is_guest',
            ),
        ));

        $this->add(array(
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
                                array('name' => 'typeahead_person'),
                            ),
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
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
                    'options' => array(
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
                    'options' => array(
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
                    'options' => array(
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
        ));

        $optionsForm = $this->addFieldset('Options', 'options_form');

        if ($this->event->getOptions()->isEmpty()) {
            $optionsForm->add(array(
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
                'options' => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'ticket_number_tickets',
                                'options' => array(
                                    'event' => $this->event,
                                ),
                            ),
                        ),
                    ),
                ),
            ));

            if (!$this->event->isOnlyMembers()) {
                $optionsForm->add(array(
                    'type'       => 'select',
                    'name'       => 'number_non_member',
                    'label'      => 'Number Non Member',
                    'attributes' => array(
                        'class'      => 'ticket_option',
                        'id'         => 'number_non_member',
                        'data-price' => $this->event->getPriceNonMembers(),
                        'options'    => $this->getNumberOptions(),
                    ),
                    'options' => array(
                        'input' => array(
                            'validators' => array(
                                array(
                                    'name'    => 'ticket_number_tickets',
                                    'options' => array(
                                        'event' => $this->event,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ));
            }
        } else {
            foreach ($this->event->getOptions() as $option) {
                $optionsForm->add(array(
                    'type'       => 'select',
                    'name'       => 'option_' . $option->getId() . '_number_member',
                    'label'      => ucfirst($option->getName()) . ' (Member)',
                    'attributes' => array(
                        'class'      => 'ticket_option',
                        'id'         => 'option_' . $option->getId() . '_number_member',
                        'data-price' => $option->getPriceMembers(),
                        'options'    => $this->getNumberOptions(),
                    ),
                    'options' => array(
                        'input' => array(
                            'validators' => array(
                                array(
                                    'name'    => 'ticket_number_tickets',
                                    'options' => array(
                                        'event' => $this->event,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ));

                if (!$this->event->isOnlyMembers()) {
                    $optionsForm->add(array(
                        'type'       => 'select',
                        'name'       => 'option_' . $option->getId() . '_number_non_member',
                        'label'      => ucfirst($option->getName()) . ' (Non Member)',
                        'attributes' => array(
                            'class'      => 'ticket_option',
                            'id'         => 'option_' . $option->getId() . '_number_non_member',
                            'data-price' => $option->getPriceNonMembers(),
                            'options'    => $this->getNumberOptions(),
                        ),
                        'options' => array(
                            'input' => array(
                                'validators' => array(
                                    array(
                                        'name'    => 'ticket_number_tickets',
                                        'options' => array(
                                            'event' => $this->event,
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ));
                }
            }
        }

        $this->add(array(
            'type'       => 'checkbox',
            'name'       => 'payed',
            'label'      => 'Payed',
            'attributes' => array(
                'id' => 'payed',
            ),
        ));

        $this->addSubmit('Sale', 'sale_tickets', 'sale', array('id' => 'sale_tickets'));
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

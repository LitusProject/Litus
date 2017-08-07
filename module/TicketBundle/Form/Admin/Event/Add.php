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

namespace TicketBundle\Form\Admin\Event;

use Ticketbundle\Entity\Event;

/**
 * Add Event
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'TicketBundle\Hydrator\Event';

    public function init()
    {
        parent::init();

        $this->setAttribute('class', $this->getAttribute('class') . ' half_width');

        $this->add(array(
            'type'       => 'select',
            'name'       => 'event',
            'label'      => 'Event',
            'required'   => true,
            'attributes' => array(
                'options' => $this->createEventsArray(),
            ),
            'options' => array(
                'input' => array(
                    'filter' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array('name' => 'ticket_activtiy'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'     => 'checkbox',
            'name'     => 'active',
            'label'    => 'Active',
            'required' => false,
        ));

        $this->add(array(
            'type'     => 'checkbox',
            'name'     => 'bookable',
            'label'    => 'Bookable',
            'required' => false,
        ));

        $this->add(array(
            'type'     => 'checkbox',
            'name'     => 'bookable_praesidium',
            'label'    => 'Bookable For Praesidium',
            'required' => false,
        ));

        $this->add(array(
            'type'       => 'datetime',
            'name'       => 'bookings_close_date',
            'label'      => 'Booking Close Date',
            'required'   => false,
            'options' => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'date',
                            'options' => array(
                                'format' => 'd/m/Y H:i',
                            ),
                        ),
                        array(
                            'name' => 'date_compare',
                            'options' => array(
                                'first_date' => 'now',
                                'format' => 'd/m/Y H:i',
                            ),
                        ),
                        array(
                            'name' => 'ticket_date',
                            'options' => array(
                                'format' => 'd/m/Y H:i',
                            ),
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'     => 'checkbox',
            'name'     => 'generate_tickets',
            'label'    => 'Generate Tickets (needed to print out ticket)',
            'required' => false,
        ));

        $this->add(array(
            'type'     => 'text',
            'name'     => 'number_of_tickets',
            'label'    => 'Number Of Tickets (0: No Limit)',
            'value'    => 0,
            'required' => true,
            'options'  => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array('name' => 'int'),
                        array(
                            'name' => 'greaterthan',
                            'options' => array(
                                'min' => 0,
                            ),
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'     => 'text',
            'name'     => 'limit_per_person',
            'label'    => 'Maximum Number Of Tickets Per Person (0: No Limit)',
            'value'    => 0,
            'required' => true,
            'options'  => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array('name' => 'int'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'     => 'checkbox',
            'name'     => 'allow_remove',
            'label'    => 'Allow Users To Remove Reservations',
            'required' => false,
        ));

        $this->add(array(
            'type'     => 'checkbox',
            'name'     => 'only_members',
            'label'    => 'Only Members',
            'required' => false,
        ));

        $this->add(array(
            'type'     => 'hidden',
            'name'     => 'enable_options_hidden',
            'required' => false,
        ));

        $this->add(array(
            'type'       => 'checkbox',
            'name'       => 'enable_options',
            'label'      => 'Enable Options',
            'required'   => false,
            'attributes' => array(
                'id' => 'enable_options',
            ),
        ));

        $this->add(array(
            'type'       => 'fieldset',
            'name'       => 'prices',
            'label'      => 'Prices',
            'attributes' => array(
                'class' => 'half_width',
            ),
            'elements'   => array(
                array(
                    'type'     => 'text',
                    'name'     => 'price_members',
                    'label'    => 'Price Members',
                    'required' => true,
                    'options' => array(
                        'input' => array(
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                            'validators' => array(
                                array('name' => 'price'),
                            ),
                        ),
                    ),
                ),
                array(
                    'type'       => 'text',
                    'name'       => 'price_non_members',
                    'label'      => 'Price Non Members',
                    'required'   => true,
                    'attributes' => array(
                        'class' => 'price_non_members',
                    ),
                    'options' => array(
                        'input' => array(
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                            'validators' => array(
                                array('name' => 'price'),
                            ),
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'collection',
            'name'       => 'options',
            'label'      => 'Options',
            'options'    => array(
                'count'                  => 0,
                'should_create_template' => true,
                'allow_add'              => true,
                'target_element'         => array(
                    'type' => 'ticket_event_option',
                ),
            ),
        ));

        $this->addSubmit('Add', 'shift_add');
    }

    protected function createEventsArray()
    {
        $events = $this->getEntityManager()
            ->getRepository('CalendarBundle\Entity\Node\Event')
            ->findAllActive();

        $eventsArray = array(
            '' => '',
        );
        foreach ($events as $event) {
            $eventsArray[$event->getId()] = $event->getTitle();
        }

        return $eventsArray;
    }

    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();

        if (!$this->data['generate_tickets']) {
            foreach ($specs['number_of_tickets']['validators'] as $key => $validator) {
                if ('greaterthan' == $validator['name']) {
                    unset($specs['number_of_tickets']['validators'][$key]);
                }
            }
        }

        if ((isset($this->data['enable_options']) && $this->data['enable_options']) || (isset($this->data['enable_options_hidden']) && $this->data['enable_options_hidden']) == '1') {
            unset($specs['prices']);
        } else {
            $specs['prices']['price_non_members']['required'] = !(isset($this->data['only_members']) && $this->data['only_members']);
        }

        return $specs;
    }
}

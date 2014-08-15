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

namespace TicketBundle\Form\Admin\Event;

use CommonBundle\Component\Validator\DateCompare as DateCompareValidator,
    CommonBundle\Component\Validator\Price as PriceValidator,
    TicketBundle\Component\Validator\Activity as ActivityValidator,
    TicketBundle\Component\Validator\Date as DateValidator,
    Ticketbundle\Entity\Event;

/**
 * Add Event
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
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
        ));

        $this->add(array(
            'type'  => 'checkbox',
            'name'  => 'active',
            'label' => 'Active',
        ));

        $this->add(array(
            'type'  => 'checkbox',
            'name'  => 'bookable',
            'label' => 'Bookable',
        ));

        $this->add(array(
            'type'  => 'checkbox',
            'name'  => 'bookable_praesidium',
            'label' => 'Bookable For Praesidium',
        ));

        $this->add(array(
            'type'       => 'datetime',
            'name'       => 'bookings_close_date',
            'label'      => 'Booking Close Date',
        ));

        $this->add(array(
            'type'  => 'checkbox',
            'name'  => 'generate_tickets',
            'label' => 'Generate Tickets (needed to print out ticket)',
        ));

        $this->add(array(
            'type'  => 'text',
            'name'  => 'number_of_tickets',
            'label' => 'Number Of Tickets (0: No Limit)',
            'value' => 0,
        ));

        $this->add(array(
            'type'  => 'text',
            'name'  => 'limit_per_person',
            'label' => 'Maximum Number Of Tickets Per Person (0: No Limit)',
            'value' => 0,
        ));

        $this->add(array(
            'type'  => 'checkbox',
            'name'  => 'allow_remove',
            'label' => 'Allow Users To Remove Reservations',
        ));

        $this->add(array(
            'type'  => 'checkbox',
            'name'  => 'only_members',
            'label' => 'Only Members',
        ));

        $this->add(array(
            'type' => 'hidden',
            'name' => 'enable_options_hidden',
        ));

        $this->add(array(
            'type'  => 'checkbox',
            'name'  => 'enable_options',
            'label' => 'Enable Options',
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
                ),
                array(
                    'type'       => 'text',
                    'name'       => 'price_non_members',
                    'label'      => 'Price Non Members',
                    'required'   => true,
                    'attributes' => array(
                        'class' => 'price_non_members',
                    )
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'collection',
            'name'       => 'options',
            'label'      => 'Options',
            'attributes' => array(
                'class' => 'half_width'
            ),
            'options'    => array(
                'count'                  => 0,
                'should_create_template' => true,
                'allow_add'              => true
                'target_element'         => array(
                    'type' => 'TicketBundle\Form\Admin\Event\Option',
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
            '' => ''
        );
        foreach ($events as $event)
            $eventsArray[$event->getId()] = $event->getTitle();

        return $eventsArray;
    }

    public function getInputFilterSpecification()
    {
        $inputs = array(
            array(
                'name'     => 'event',
                'required' => true,
                'filter' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    new ActivityValidator($this->getEntityManager()),
                ),
            ),
            array(
                'name'     => 'bookings_close_date',
                'required' => false,
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
                    new DateCompareValidator('now', 'd/m/Y H:i'),
                    new DateValidator($this->getEntityManager(), 'd/m/Y H:i'),
                ),
            )
        );

        if (isset($this->data['generate_tickets']) && $this->data['generate_tickets']) {
            $inputs[] = array(
                'name'     => 'number_of_tickets',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array('name' => 'int'),
                    array(
                        'name' => 'greaterthan',
                        'options' => array(
                            'min' => 0,
                        )
                    )
                ),
            );
        } else {
            $inputs[] = array(
                'name'     => 'number_of_tickets',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array('name' => 'int'),
                ),
            );
        }

        $inputs[] = array(
            'name'     => 'limit_per_person',
            'required' => true,
            'filters'  => array(
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array('name' => 'int'),
            ),
        );

        $inputs[] = array(
            'name'     => 'enable_options_hidden',
            'required' => false,
        );

        if ((!isset($this->data['enable_options']) || !$this->data['enable_options']) &&
            (!isset($this->data['enable_options_hidden']) || $this->data['enable_options_hidden'] != '1')) {
            $inputs['prices'] = array(
                array(
                    'name'     => 'price_members',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new PriceValidator()
                    ),
                ),
                array(
                    'name'     => 'price_non_members',
                    'required' => isset($this->data['only_members']) && $this->data['only_members'] ? false : true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new PriceValidator()
                    ),
                ),
            );
        }

        return $inputs;
    }
}

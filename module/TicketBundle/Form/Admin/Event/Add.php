<?php

namespace TicketBundle\Form\Admin\Event;

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

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'event',
                'label'      => 'Event',
                'required'   => true,
                'attributes' => array(
                    'options' => $this->createEventsArray(),
                ),
                'options'    => array(
                    'input' => array(
                        'filter' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Activity'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'form',
                'label'      => 'Form',
                'required'   => false,
                'attributes' => array(
                    'options' => $this->createFormArray(),
                ),
                'options'    => array(
                    'input' => array(
                        'filter' => array(
                            array('name' => 'StringTrim'),
                        ),
            //                        'validators' => array(
            //                            array('name' => 'Activity'),
            //                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'checkbox',
                'name'     => 'active',
                'label'    => 'Active',
                'required' => false,
            )
        );

        $this->add(
            array(
                'type'     => 'checkbox',
                'name'     => 'visible',
                'label'    => 'Visible',
                'required' => false,
            )
        );

        $this->add(
            array(
                'type'     => 'checkbox',
                'name'     => 'bookable',
                'label'    => 'Bookable',
                'required' => false,
            )
        );

        $this->add(
            array(
                'type'     => 'checkbox',
                'name'     => 'bookable_praesidium',
                'label'    => 'Bookable For Praesidium',
                'required' => false,
            )
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'mail_from',
                'label'      => 'Email',
                'required'   => false,
                'attributes' => array(
                    'id' => 'mail_from',
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
        );

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'mail_confirmation_subject',
                'label'    => 'Subject of confirmation mail<br>Has to contain {{ event }}',
                'required' => false,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            ),
        );

        $this->add(
            array(
                'type'     => 'textarea',
                'name'     => 'mail_confirmation_body',
                'label'    => 'Body of the confirmation mail<br>Has to contain {{ fullname }}<br>{{ event }}<br>{{ option }}<br>{{ paylink }}',
                'required' => false,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            ),
        );

        $this->add(
            array(
                'type'     => 'checkbox',
                'name'     => 'deadline_enabled',
                'label'    => 'Payable after 24 hours',
                'required' => false,
            )
        );

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'deadline_time',
                'label'    => 'Minutes that the link is valid',
                'required' => false,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Int'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'datetime',
                'name'     => 'bookings_close_date',
                'label'    => 'Bookings Close Date',
                'required' => false,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name'    => 'Date',
                                'options' => array(
                                    'format' => 'd/m/Y H:i',
                                ),
                            ),
                            array(
                                'name'    => 'DateCompare',
                                'options' => array(
                                    'first_date' => 'now',
                                    'format'     => 'd/m/Y H:i',
                                ),
                            ),
            //                            array(
            //                                'name'    => 'BookingCloseData',
            //                                'options' => array(
            //                                    'format' => 'd/m/Y H:i',
            //                                ),
            //                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'checkbox',
                'name'     => 'qr_enabled',
                'label'    => 'Enable qr code for tickets',
                'required' => false,
            )
        );

//        $this->add(
//            array(
//                'type'     => 'checkbox',
//                'name'     => 'generate_tickets',
//                'label'    => 'Generate Tickets (needed to print out ticket)',
//                'required' => false,
//            )
//        );

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'number_of_tickets',
                'label'    => 'Number Of Tickets (0: No Limit)',
                'value'    => 0,
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'int'),
                            array(
                                'name'    => 'greaterthan',
                                'options' => array(
                                    'min' => 0,
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'limit_per_person',
                'label'    => 'Maximum Number Of Tickets Per Person (0: No Limit)',
                'value'    => 0,
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'int'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'checkbox',
                'name'     => 'allow_remove',
                'label'    => 'Allow Users To Remove Reservations',
                'required' => false,
            )
        );

        $this->add(
            array(
                'type'     => 'checkbox',
                'name'     => 'only_members',
                'label'    => 'Only Members',
                'required' => false,
            )
        );

        $this->add(
            array(
                'type'     => 'checkbox',
                'name'     => 'online_payment',
                'label'    => 'Online Payment',
                'required' => false,
            )
        );

        $this->add(
            array(
                'type'    => 'text',
                'name'    => 'invoice_base_id',
                'label'   => 'Invoice Base ID',
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'InvoiceBase'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'    => 'text',
                'name'    => 'order_base_id',
                'label'   => 'Order Base ID',
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'terms_url',
                'label'    => 'Link to Terms and Conditions',
                'required' => false,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            ),
        );

        $this->add(
            array(
                'type'     => 'hidden',
                'name'     => 'enable_options_hidden',
                'required' => false,
            )
        );

        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'enable_options',
                'label'      => 'Enable Options',
                'required'   => false,
                'attributes' => array(
                    'id' => 'enable_options',
                ),
            )
        );

        $this->add(
            array(
                'type'    => 'textarea',
                'name'    => 'description',
                'label'   => 'Description',
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
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
                        'required' => false,
                        'options'  => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                                'validators' => array(
                                    array('name' => 'Price'),
                                ),
                            ),
                        ),
                    ),
                    array(
                        'type'       => 'text',
                        'name'       => 'price_non_members',
                        'label'      => 'Price Non Members',
                        'attributes' => array(
                            'class' => 'price_non_members',
                        ),
                        'options'    => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                                'validators' => array(
                                    array('name' => 'Price'),
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'    => 'collection',
                'name'    => 'options',
                'label'   => 'Options',
                'options' => array(
                    'count'                  => 0,
                    'should_create_template' => true,
                    'allow_add'              => true,
                    'target_element'         => array(
                        'type' => 'ticket_event_option',
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'shift_add');
    }

    protected function createEventsArray()
    {
        $events = $this->getEntityManager()
            ->getRepository('CalendarBundle\Entity\Node\Event')
            ->findAllActive(50);

        $eventsArray = array(
            '' => '',
        );
        foreach ($events as $event) {
            $eventsArray[$event->getId()] = $event->getTitle();
        }

        return $eventsArray;
    }

    protected function createFormArray()
    {
        $forms = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\Form')
            ->findAllActive();

        $formArray = array(
            '' => '',
        );
        foreach ($forms as $form) {
            $formArray[$form->getId()] = $form->getTitle();
        }

        return $formArray;
    }

    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();
        if (!$this->data['generate_tickets']) {
            foreach ($specs['number_of_tickets']['validators'] as $key => $validator) {
                if ($validator['name'] == 'greaterthan') {
                    unset($specs['number_of_tickets']['validators'][$key]);
                }
            }
        }

        if ((isset($this->data['enable_options']) && $this->data['enable_options']) || (isset($this->data['enable_options_hidden']) && $this->data['enable_options_hidden']) == '1') {
            unset($specs['prices']);
        } else {
//            $specs['prices']['price_non_members']['required'] = !(isset($this->data['only_members']) && $this->data['only_members']);
        }

        return $specs;
    }
}

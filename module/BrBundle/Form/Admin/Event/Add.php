<?php

namespace BrBundle\Form\Admin\Event;

use BrBundle\Entity\Event;

/**
 * Add a corporate relations event.
 *
 * @author Matthias Swiggers <matthias.swiggers@vtk.be>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'BrBundle\Hydrator\Event';

    /**
     * @var Event|null
     */
    protected $event;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'title',
                'label'    => 'Event Title',
                'required' => true,
                'options'  => array(
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
                'type'       => 'textarea',
                'name'       => 'description_for_students',
                'label'      => 'Description for students',
                'attributes' => array(
                    'id' => 'description_for_companies',
                ),
                'options'    => array(
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
                'type'       => 'textarea',
                'name'       => 'description_for_companies',
                'label'      => 'Description for companies',
                'attributes' => array(
                    'id' => 'description_for_companies',
                ),
                'options'    => array(
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
                'type'    => 'textarea',
                'name'    => 'view_information_nl',
                'label'   => 'Information displayed on the page in NL',
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
                'type'    => 'textarea',
                'name'    => 'view_information_en',
                'label'   => 'Information displayed on the page in EN',
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
                'type'     => 'datetime',
                'name'     => 'start_date',
                'label'    => 'Start Date',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'Date',
                                'options' => array(
                                    'format' => 'd/m/Y H:i',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'datetime',
                'name'     => 'end_date',
                'label'    => 'End Date',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'DateCompare',
                                'options' => array(
                                    'first_date' => 'start_date',
                                    'format'     => 'd/m/Y H:i',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );


        $this->add(
            array(
                'type'     => 'datetime',
                'name'     => 'end_date_visible',
                'label'    => 'End Date Visible',
                'required' => false,
                'options'  => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'DateCompare',
                                'options' => array(
                                    'first_date' => 'end_date',
                                    'format'     => 'd/m/Y H:i',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'    => 'datetime',
                'name'    => 'subscription_date',
                'label'   => 'Subscription opening date',
                'options' => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'Date',
                                'options' => array(
                                    'format' => 'd/m/Y H:i',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'    => 'datetime',
                'name'    => 'mapview_date',
                'label'   => 'Map viewing date',
                'options' => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'Date',
                                'options' => array(
                                    'format' => 'd/m/Y H:i',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'    => 'text',
                'name'    => 'nb_students',
                'label'   => 'Amount of Students',
                'options' => array(
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
                'type'    => 'text',
                'name'    => 'nb_companies',
                'label'   => 'Amount of Companies',
                'options' => array(
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
                'type'    => 'textarea',
                'name'    => 'audience',
                'label'   => 'Audience',
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
                'type'    => 'textarea',
                'name'    => 'location',
                'label'   => 'Location',
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
                'type'       => 'text',
                'name'       => 'food',
                'label'      => 'Food options',
                'attributes' => array(
                    'data-help' => 'The options comma separated.',
                ),
                'options'    => array(
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
                'type'     => 'checkbox',
                'name'     => 'visible_for_companies',
                'label'    => 'Visible for Companies',
                'required' => true,
            )
        );

        $this->add(
            array(
                'type'     => 'checkbox',
                'name'     => 'visible_for_students',
                'label'    => 'Visible for Students',
                'required' => true,
            )
        );

        $this->addSubmit('Add', 'mail_add');

        if ($this->event !== null) {
            $this->bind($this->event);
        }
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

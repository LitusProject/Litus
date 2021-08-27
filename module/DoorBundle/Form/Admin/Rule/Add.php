<?php

namespace DoorBundle\Form\Admin\Rule;

/**
 * Add Key
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'DoorBundle\Hydrator\Rule';

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'typeahead',
                'name'     => 'academic',
                'label'    => 'Academic',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'validators' => array(
                            array('name' => 'TypeaheadPerson'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'date',
                'name'     => 'start_date',
                'label'    => 'Start Date',
                'required' => true,
            )
        );

        $this->add(
            array(
                'type'     => 'date',
                'name'     => 'end_date',
                'label'    => 'End Date',
                'required' => true,
            )
        );

        $this->add(
            array(
                'type'     => 'time',
                'name'     => 'start_time',
                'label'    => 'Start Time',
                'required' => true,
            )
        );

        $this->add(
            array(
                'type'     => 'time',
                'name'     => 'end_time',
                'label'    => 'End Time',
                'required' => true,
            )
        );

        $this->addSubmit('Add', 'rule_add');
    }
}

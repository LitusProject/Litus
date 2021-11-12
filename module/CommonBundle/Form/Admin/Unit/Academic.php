<?php

namespace CommonBundle\Form\Admin\Unit;

/**
 * The form used to add an academic member to a unit.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Academic extends \CommonBundle\Component\Form\Admin\Form
{
    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'typeahead',
                'name'     => 'person',
                'label'    => 'Name',
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
                'type'  => 'checkbox',
                'name'  => 'coordinator',
                'label' => 'Coordinator',
            )
        );

        $this->add(
            array(
                'type'  => 'text',
                'name'  => 'description',
                'label' => 'Description',
            )
        );

        $this->add(
            array(
                'type'  => 'hidden',
                'name'  => 'mapType',
                'value' => 'academic',
            )
        );

        $this->addSubmit('Add', 'unit_add');
    }
}

<?php

namespace CommonBundle\Form\Admin\Person;

/**
 * Edit Person
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
abstract class Edit extends \CommonBundle\Form\Admin\Person\Add
{
    public function init()
    {
        parent::init();

        $this->remove('username');

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'system_roles',
                'label'      => 'System Groups',
                'attributes' => array(
                    'disabled' => true,
                    'multiple' => true,
                    'options'  => $this->createRolesArray(true),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'unit_roles',
                'label'      => 'Unit Groups',
                'attributes' => array(
                    'disabled' => true,
                    'multiple' => true,
                    'options'  => $this->createRolesArray(),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'code',
                'label'      => 'Code',
                'attributes' => array(
                    'disabled' => true,
                ),
            )
        );
    }
}

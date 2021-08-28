<?php

namespace CommonBundle\Form\Admin\Role;

/**
 * Edit Role
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends \CommonBundle\Form\Admin\Role\Add
{
    public function init()
    {
        parent::init();

        $this->remove('name');

        $this->remove('submit')
            ->addSubmit('Save', 'role_edit');
    }
}

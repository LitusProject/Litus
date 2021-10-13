<?php

namespace BrBundle\Form\Admin\Company\User;

/**
 * Edit a user's data.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \CommonBundle\Form\Admin\Person\Edit
{
    protected $hydrator = 'BrBundle\Hydrator\User\Person\Corporate';

    public function init()
    {
        parent::init();

        $this->remove('system_roles')
            ->remove('unit_roles')
            ->remove('roles');

        $this->remove('submit')
            ->addSubmit('Save', 'user_edit');
    }
}

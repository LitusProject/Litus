<?php

namespace CudiBundle\Form\Admin\Supplier\User;

/**
 * Edit a user's data.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \CommonBundle\Form\Admin\Person\Edit
{
    protected $hydrator = 'CudiBundle\Hydrator\User\Person\Supplier';

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

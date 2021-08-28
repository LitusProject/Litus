<?php

namespace CudiBundle\Form\Admin\Supplier\User;

/**
 * Add a user to the database.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Form\Admin\Person\Add
{
    protected $hydrator = 'CudiBundle\Hydrator\User\Person\Supplier';

    public function init()
    {
        parent::init();

        $this->remove('roles');

        $this->remove('submit')
            ->addSubmit('Add', 'user_add');
    }
}

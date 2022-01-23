<?php

namespace BrBundle\Form\Admin\Company\User;

/**
 * Add User
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Form\Admin\Person\Add
{
    protected $hydrator = 'BrBundle\Hydrator\User\Person\Corporate';

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'activate',
                'label' => 'Activation Mail',
                'value' => true,
            )
        );

        $this->remove('roles');

        $this->remove('submit')
            ->addSubmit('Add', 'user_add');
    }
}

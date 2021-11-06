<?php

namespace CommonBundle\Form\Admin\Auth;

/**
 * Login
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Login extends \CommonBundle\Component\Form\Admin\Form
{
    public function init()
    {
        parent::init();

        $this->setAttribute('id', 'login');

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'username',
                'attributes' => array(
                    'placeholder' => 'username',
                    'autofocus'   => true,
                    'id'          => 'username',
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'password',
                'name'       => 'password',
                'attributes' => array(
                    'placeholder' => 'password',
                    'id'          => 'password',
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'remember_me',
                'label'      => 'Remember Me',
                'attributes' => array(
                    'id' => 'remember_me',
                ),
            )
        );
    }
}

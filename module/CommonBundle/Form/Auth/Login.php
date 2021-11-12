<?php

namespace CommonBundle\Form\Auth;

/**
 * Authentication login form.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Login extends \CommonBundle\Component\Form\Bootstrap\Form
{
    public function init()
    {
        parent::init();

        $this->setAttribute('id', 'login');

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'username',
                'label'    => 'Username',
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
                'type'     => 'password',
                'name'     => 'password',
                'label'    => 'Password',
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
                'type'  => 'checkbox',
                'name'  => 'remember_me',
                'label' => 'Remember Me',
            )
        );

        $this->addSubmit('Login', 'btn btn-default pull-right');
    }
}

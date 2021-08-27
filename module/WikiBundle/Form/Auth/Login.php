<?php

namespace WikiBundle\Form\Auth;

/**
 * Wiki login form.
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Login extends \CommonBundle\Component\Form\Bootstrap\Form
{
    /**
     * @var string
     */
    private $username;

    public function init()
    {
        parent::init();

        $this->setAttribute('id', 'login');

        if ($this->username !== null) {
            $this->add(
                array(
                    'type'       => 'text',
                    'name'       => 'username_visible',
                    'label'      => 'Username',
                    'value'      => $this->username,
                    'attributes' => array(
                        'disabled' => 'disabled',
                    ),
                    'options' => array(
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
                    'type'  => 'hidden',
                    'name'  => 'username',
                    'value' => $this->username,
                )
            );
        } else {
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
        }

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
                'type'  => 'hidden',
                'name'  => 'remember_me',
                'value' => true,
            )
        );

        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'remember_me_visible',
                'label'      => 'Remember Me',
                'value'      => true,
                'attributes' => array(
                    'disabled' => 'disabled',
                ),
            )
        );

        $this->addSubmit('Login', 'btn btn-default pull-right');
    }

    /**
     * @param  string $username
     * @return self
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }
}

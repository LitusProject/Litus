<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

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
    private $_username;

    public function init()
    {
        parent::init();

        $this->setAttribute('id', 'login');

        if (null !== $this->_username) {
            $this->add(array(
                'type'       => 'text',
                'name'       => 'username_visible',
                'label'      => 'Username',
                'value'      => $this->_username,
                'attributes' => array(
                    'disabled' => 'disabled',
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            ));

            $this->add(array(
                'type'  => 'hidden',
                'name'  => 'username',
                'value' => $this->_username,
            ));
        } else {
            $this->add(array(
                'type'       => 'text',
                'name'       => 'username',
                'label'      => 'Username',
                'required'   => true,
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            ));
        }

        $this->add(array(
            'type'       => 'password',
            'name'       => 'password',
            'label'      => 'Password',
            'required'   => true,
            'options'    => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'hidden',
            'name'       => 'remember_me',
            'value'      => true,
        ));

        $this->add(array(
            'type'       => 'checkbox',
            'name'       => 'remember_me_visible',
            'label'      => 'Remember Me',
            'value'      => true,
            'attributes' => array(
                'disabled' => 'disabled',
            ),
        ));

        $this->addSubmit('Login', 'btn btn-default pull-right');
    }

    /**
     * @param  string $username
     * @return self
     */
    public function setUsername($username)
    {
        $this->_username = $username;

        return $this;
    }
}

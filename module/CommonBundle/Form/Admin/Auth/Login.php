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

        $this->add(array(
            'type'       => 'text',
            'name'       => 'username',
            'attributes' => array(
                'placeholder' => 'username',
                'autofocus'   => true,
                'id'          => 'username',
            ),
        ));

        $this->add(array(
            'type'       => 'password',
            'name'       => 'password',
            'attributes' => array(
                'placeholder' => 'password',
                'id'          => 'password',
            ),
        ));

        $this->add(array(
            'type'       => 'checkbox',
            'name'       => 'remember_me',
            'label'      => 'Remember Me',
            'attributes' => array(
                'id' => 'remember_me',
            )
        ));
    }
}

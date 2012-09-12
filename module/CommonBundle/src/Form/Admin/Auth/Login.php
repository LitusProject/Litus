<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Form\Admin\Auth;

use CommonBundle\Component\Form\Admin\Element\Checkbox,
    CommonBundle\Component\Form\Admin\Element\Password,
    CommonBundle\Component\Form\Admin\Element\Text;

/**
 * Login
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Login extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @param mixed $opts The form's options
     */
    public function __construct($opts = null)
    {
        parent::__construct($opts);

        $this->setAttribute('id', 'login');

        $field = new Text('username');
        $field->setAttribute('placeholder', 'username')
            ->setAttribute('autofocus', 'autofocus');
        $this->add($field);

        $field = new Password('password');
        $field->setAttribute('placeholder', 'password');
        $this->add($field);

        $field = new Checkbox('remember_me');
        $field->setLabel('Remember Me');
        $this->add($field);
    }
}

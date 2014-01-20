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
class Login extends \CommonBundle\Form\Auth\Login
{
    /**
     * @param string $action
     * @param null|string|int $name Optional name for the element
     */
    public function __construct($action = '', $name = null)
    {
        parent::__construct($action, $name);

        $this->get('remember_me')
            ->setValue(true)
            ->setAttribute('disabled', 'disabled');
    }

    public function setUsername($username)
    {
        $this->get('username')
            ->setValue($username)
            ->setAttribute('disabled', 'disabled');
    }
}

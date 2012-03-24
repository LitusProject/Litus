<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
 
namespace CommonBundle\Form\Auth;

use CommonBundle\Component\Form\Bootstrap\Element\Submit,
    CommonBundle\Component\Form\Bootstrap\Element\Password,
    CommonBundle\Component\Form\Bootstrap\Element\Text;

/**
 * Authentication login form.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Login extends \CommonBundle\Component\Form\Bootstrap\Form
{
	/**
	 * @param string $action
	 * @param mixed $options The form's options
	 */
    public function __construct($action = '', $options = null)
    {
        parent::__construct($options);

        $this->setAction($action);

        $this->setAttrib('id', 'login')
            ->setAttrib('class', 'form-inline');

        $field = new Text('username');
        $field->setLabel('Username')
            ->setRequired(true);
        $this->addElement($field);

        $field = new Password('password');
        $field->setLabel('Password')
            ->setRequired(true);
        $this->addElement($field);
        
        $field = new Submit('submit');
        $field->setLabel('Login')
            ->setAttrib('class', 'btn pull-right');
        $this->addElement($field);
    }
}
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
 
namespace CommonBundle\Form\Admin\Auth;

use Zend\Form\Form;,
	Zend\Form\Element\Password;,
	Zend\Form\Element\Text;

/**
 * Authentication login form.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Login extends \CommonBundle\Component\Form\Form
{
	/**
	 * @param mixed $options The form's options
	 */
    public function __construct($options = null)
    {
        parent::__construct($options);

        $this->setAttrib('id', 'login');

        $field = new Text('username');
        $field->setAttrib('placeholder', 'username')
            ->setDecorators(array('ViewHelper', 'Errors'));
        $this->addElement($field);

        $field = new Password('password');
        $field->setAttrib('placeholder', 'password')
            ->setDecorators(array('ViewHelper', 'Errors'));
        $this->addElement($field);
    }
}
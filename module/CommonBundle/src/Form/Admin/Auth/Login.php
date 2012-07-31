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

use Zend\Form\Element\Checkbox,
    Zend\Form\Element\Password,
    Zend\Form\Element\Text;

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

        $this->setAttrib('id', 'login');

        $field = new Text('username');
        $field->setAttrib('placeholder', 'username')
            ->setAttrib('autofocus', 'autofocus')
            ->setDecorators(array('ViewHelper', 'Errors'));
        $this->addElement($field);

        $field = new Password('password');
        $field->setAttrib('placeholder', 'password')
            ->setDecorators(array('ViewHelper', 'Errors'));
        $this->addElement($field);
        
        $field = new Checkbox('remember_me');
        $field->setLabel('Remember Me')
            ->setDecorators(
                array(
                    array('ViewHelper'),
                    array('Errors'),
                    array('Label', array('placement' => 'APPEND'))
                )
            );
        $this->addElement($field);
    }
}

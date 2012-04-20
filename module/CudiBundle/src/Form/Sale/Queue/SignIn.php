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
 
namespace CudiBundle\Form\Sale\Queue;

use CommonBundle\Component\Form\Bootstrap\Element\Reset,
	CommonBundle\Component\Form\Bootstrap\Element\Button,
	CommonBundle\Component\Form\Bootstrap\Element\Text;

/**
 * Sign in to queue
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SignIn extends \CommonBundle\Component\Form\Bootstrap\Form
{
    public function __construct($opts = null )
    {
        parent::__construct($opts);

        $field = new Text('username');
        $field->setLabel('Student Number')
            ->setRequired()
			->setAttrib('id', 'username')
			->setAttrib('placeholder', "Student Number")
			->setAttrib('autocomplete', 'off');
        $this->addElement($field);
      	
        $field = new Button('submit');
        $field->setLabel('Sign In')
        	->addDecorator('ViewHelper')
        	->setAttrib('id', 'signin');
        $this->addElement($field);
        
        $field = new Reset('cancel');
        $field->setLabel('Cancel');
        $this->addElement($field);
        
        $this->setActionsGroup(array('submit', 'cancel'));
    }
}

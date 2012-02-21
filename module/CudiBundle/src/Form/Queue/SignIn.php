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
 
namespace CudiBundle\Form\Queue;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
	CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
	Zend\Form\Element\Submit,
	Zend\Form\Element\Text;
	
class SignIn extends \CommonBundle\Component\Form\Admin\Form
{
    public function __construct($opts = null )
    {
        parent::__construct($opts);

        $field = new Text('number');
        $field->setLabel('Student Number')
        	->setAttrib('size', 30)
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);
        
        $field = new Submit('submit');
        $field->setLabel('Sign In')
	        ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }
}

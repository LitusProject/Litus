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
 
namespace CudiBundle\Form\Admin\Article\Comment;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
	CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
	Zend\Form\Element\Select,
	Zend\Form\Element\Submit,
	Zend\Form\Element\TextArea;

/**
 * Add Comment
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    public function __construct($options = null)
    {
        parent::__construct($options);
             
        $field = new TextArea('text');
        $field->setLabel('Comment')
        	->setRequired()
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);
        
        $field = new Select('type');
        $field->setLabel('Type')
            ->setMultiOptions(array('internal' => 'Internal', 'external' => 'External'))
        	->setRequired()
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);
        
        $field = new Submit('submit');
        $field->setLabel('Add')
                ->setAttrib('class', 'comment_add')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }
}
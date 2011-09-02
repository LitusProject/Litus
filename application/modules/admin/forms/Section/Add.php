<?php

namespace Admin\Form\Section;

use \Litus\Form\Decorator\ButtonDecorator;
use \Litus\Form\Decorator\FieldDecorator;

use \Zend\Form\Form;
use \Zend\Form\Element\Submit;
use \Zend\Form\Element\Text;
use \Zend\Form\Element\Textarea;

class Add extends \Litus\Form\Form
{
    public function __construct($options = null)
    {
        parent::__construct($options);

        $this->setAction('/admin/section/add');
        $this->setMethod('post');

        $field = new Text('name');
        $field->setLabel('Name')
                ->setRequired()
                ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Textarea('content');
        $field->setLabel('Content')
                ->setRequired()
                ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Submit('submit');
        $field->setLabel('Add')
                ->setAttrib('class', 'sections_add')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }
}
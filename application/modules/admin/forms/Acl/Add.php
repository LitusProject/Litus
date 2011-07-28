<?php

namespace Admin\Form\Acl;

use \Litus\Form\Decorator\ButtonDecorator;
use \Litus\Form\Decorator\FieldDecorator;

use \Zend\Form\Form;
use \Zend\Form\Element\Multiselect;
use \Zend\Form\Element\Submit;
use \Zend\Form\Element\Text;

class Add extends \Litus\Form\Form
{
    public function __construct($parentOptions, $options = null)
    {
        parent::__construct($options);

        $name = new Text('name');
        $name->setLabel('Name')
                ->setRequired()
                ->setDecorators(array(new FieldDecorator()));
        $this->addElement($name);

        $parent = new Multiselect('parents');
        $parent->setLabel('Parents')
                ->setMultiOptions($parentOptions)
                ->setDecorators(array(new FieldDecorator()));
        $this->addElement($parent);

        $submit = new Submit('submit');
        $submit->setLabel('Add')
                ->setAttrib('class', 'groups_add')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($submit);
    }
}
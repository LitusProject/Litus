<?php

namespace Admin\Form\Config;

use \Litus\Form\Admin\Decorator\ButtonDecorator;
use \Litus\Form\Admin\Decorator\FieldDecorator;

use \Zend\Form\Element\Submit;
use \Zend\Form\Element\Text;
use \Zend\Form\Element\Textarea;

class Add extends \Litus\Form\Admin\Form
{

    public function __construct($options = null)
    {
        parent::__construct($options);

        $field = new Text('prefix');
        $field->setLabel('Prefix')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Text('name');
        $field->setLabel('Name')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Textarea('value');
        $field->setLabel('Value')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Text('description');
        $field->setLabel('Description')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Submit('submit');
        $field->setLabel('Add')
            ->setAttrib('class', 'config_add')
            ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }
}

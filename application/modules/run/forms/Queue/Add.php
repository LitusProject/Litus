<?php

namespace Run\Form\Queue;

use \Litus\Form\Bootstrap\Decorator\ButtonDecorator;
use \Litus\Form\Bootstrap\Decorator\FieldDecorator;

use \Zend\Form\Form;
use \Zend\Form\Element\Submit;
use \Zend\Form\Element\Text;

class Add extends \Litus\Form\Admin\Form
{
    public function __construct($options = null)
    {
        parent::__construct($options);

        $field = new Text('university_identification');
        $field->setLabel('Student Number')
            ->setRequired()
            ->setAttrib('class', 'xlarge span2')
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Text('first_name');
        $field->setLabel('First Name')
            ->setRequired()
            ->setAttrib('class', 'xlarge span3')
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Text('last_name');
        $field->setLabel('Last Name')
            ->setRequired()
            ->setAttrib('class', 'xlarge span3')
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $this->addDisplayGroup(
            array(
                'university_identification',
                'first_name',
                'last_name'
            ),
            'information'
        );
        $this->getDisplayGroup('information')
            ->setLegend('Information')
            ->removeDecorator('DtDdWrapper');

        $field = new Submit('submit');
        $field->setLabel('Queue')
            ->setAttrib('class', 'btn primary')
            ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }
}
<?php

namespace Pdf\Form\Br;

use \Litus\Form\Decorator\ButtonDecorator;
use \Litus\Form\Decorator\FieldDecorator;
use \Litus\Form\Form;

use \Zend\Form\Element\Select;
use \Zend\Form\Element\Submit;
use \Zend\Registry;

class Index extends Form
{
    public function __construct($ids, $options = null)
    {
        parent::__construct($options);

        $this->setAction('/pdf/br/view');
        $this->setMethod('post');

        $options = array();
        foreach ($ids as $id)
            $options[$id] = $id;
        
        $field = new Select('id');
        $field->setLabel('ID #')
            ->setRequired()
            ->setMultiOptions($options)
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Submit('submit');
        $field->setLabel('View')
            ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }
}
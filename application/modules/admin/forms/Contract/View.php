<?php

namespace Admin\Form\Contract;

use \Litus\Form\Decorator\ButtonDecorator;
use \Litus\Form\Decorator\FieldDecorator;
use \Litus\Form\Form;

use \Zend\Form\Element\Select;
use \Zend\Form\Element\Submit;
use \Zend\Form\Element\Hidden;
use \Zend\Registry;

class View extends Form{

    public function __construct($id, $types, $options = null)
    {
        parent::__construct($options);

        $this->setAction('/admin/contract/download');
        $this->setMethod('post');

        $field = new Hidden('id');
        $field->setValue($id);
        $this->addElement($field);

        $options = array();
        foreach ($types as $type)
            $options[$type] = $type;

        $field = new Select('type');
        $field->setLabel('Type')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()))
            ->setMultiOptions($options);
        $this->addElement($field);

        $field = new Submit('submit');
        $field->setLabel('Download')
            ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }
}

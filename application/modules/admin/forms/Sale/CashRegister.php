<?php

namespace Admin\Form\Sale;

use \Litus\Form\Admin\Decorator\ButtonDecorator;
use \Litus\Form\Admin\Decorator\FieldDecorator;
use \Litus\Application\Resource\Doctrine as DoctrineResource;

use \Zend\Form\Form;
use \Zend\Form\Element\Hidden;
use \Zend\Form\Element\Submit;
use \Zend\Form\Element\Text;
use \Zend\Form\Element\Textarea;
use \Zend\Validator;
use \Zend\Registry;

class CashRegister extends \Litus\Form\Admin\Form
{
    public function __construct($options = null )
    {
        parent::__construct($options);

        $field = new Hidden('name');
        $field->setValue('changeme456789352');
        $this->addElement($field);

        $field = new Text('500p');
        $field->setLabel('€ 500.00')
            ->setRequired()
            ->addValidator( new \Zend\Validator\Int() )
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Text('200p');
        $field->setLabel('€ 200.00')
            ->setRequired()
            ->addValidator( new \Zend\Validator\Int() )
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Text('100p');
        $field->setLabel('€ 100.00')
            ->setRequired()
            ->addValidator( new \Zend\Validator\Int() )
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Text('50p');
        $field->setLabel('€ 50.00')
            ->setRequired()
            ->addValidator( new \Zend\Validator\Int() )
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Text('20p');
        $field->setLabel('€ 20.00')
            ->setRequired()
            ->addValidator( new \Zend\Validator\Int() )
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Text('10p');
        $field->setLabel('€ 10.00')
            ->setRequired()
            ->addValidator( new \Zend\Validator\Int() )
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Text('5p');
        $field->setLabel('€ 5.00')
            ->setRequired()
            ->addValidator( new \Zend\Validator\Int() )
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Text('2p');
        $field->setLabel('€ 2.00')
            ->setRequired()
            ->addValidator( new \Zend\Validator\Int() )
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Text('1p');
        $field->setLabel('€ 1.00')
            ->setRequired()
            ->addValidator( new \Zend\Validator\Int() )
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Text('0p5');
        $field->setLabel('€ 0.50')
            ->setRequired()
            ->addValidator( new \Zend\Validator\Int() )
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Text('0p2');
        $field->setLabel('€ 0.20')
            ->setRequired()
            ->addValidator( new \Zend\Validator\Int() )
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Text('0p1');
        $field->setLabel('€ 0.10')
            ->setRequired()
            ->addValidator( new \Zend\Validator\Int() )
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Text('0p05');
        $field->setLabel('€ 0.05')
            ->setRequired()
            ->addValidator( new \Zend\Validator\Int() )
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Text('0p02');
        $field->setLabel('€ 0.02')
            ->setRequired()
            ->addValidator( new \Zend\Validator\Int() )
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Text('0p01');
        $field->setLabel('€ 0.01')
            ->setRequired()
            ->addValidator( new \Zend\Validator\Int() )
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Text('Bank_Device_1');
        $field->setLabel('Bank Device 1')
            ->setRequired()
            ->addValidator( new \Zend\Validator\Float() )
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Text('Bank_Device_2');
        $field->setLabel('Bank Device 2')
            ->setRequired()
            ->addValidator( new \Zend\Validator\Float() )
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);


        $field = new Submit('submit');
        $field->setLabel('Submit')
            ->setAttrib('class', 'sale_add')
            ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }

}

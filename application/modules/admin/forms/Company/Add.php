<?php

namespace Admin\Form\Company;

use \Litus\Form\Decorator\ButtonDecorator;
use \Litus\Form\Decorator\FieldDecorator;

use \Zend\Form\Form;
use \Zend\Form\Element\Submit;
use \Zend\Form\Element\Text;

class Add extends \Admin\Form\User\Add
{
    public function __construct($options = null)
    {
        parent::__construct($options);

        $this->setAction('/admin/company/add');

        $this->removeElement('roles');
        $this->removeElement('submit');

        $field = new Text('company_name');
        $field->setLabel('Company Name')
                ->setRequired()
                ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Text('vat_number');
        $field->setLabel('VAT Number')
                ->setRequired()
                ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Submit('submit');
        $field->setLabel('Add')
                ->setAttrib('class', 'companies_add')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }
}
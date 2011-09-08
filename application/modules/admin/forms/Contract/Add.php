<?php

namespace Admin\Form\Contract;

use \Litus\Form\Decorator\ButtonDecorator;
use \Litus\Form\Decorator\FieldDecorator;
use \Litus\Application\Resource\Doctrine as DoctrineResource;
use \Litus\Entity\Br\Contracts\Section;

use \Zend\Form\Form;
use \Zend\Form\Element\Multiselect;
use \Zend\Form\Element\Select;
use \Zend\Form\Element\Submit;
use \Zend\Form\Element\Text;
use \Zend\Registry;
use \Zend\Validator\Float as FloatValidator;

class Add extends \Litus\Form\Form
{
    public function __construct($options = null)
    {
        parent::__construct($options);

        $this->setAction('/admin/contract/add');
        $this->setMethod('post');

        $field = new Select('company');
        $field->setLabel('Company')
            ->setRequired()
            ->setMultiOptions($this->_getCompanies())
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Text('discount');
        $field->setLabel('Discount Percentage')
            ->setRequired()
            ->setValue('0')
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Text('title');
        $field->setLabel('Contract Title')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Multiselect('sections');
        $field->setLabel('Sections')
            ->setRequired()
            ->setMultiOptions($this->_getSections())
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Submit('submit');
        $field->setLabel('Add')
            ->setAttrib('class', 'contracts_add')
            ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }

    private function _getCompanies()
    {
        $companies = Registry::get(DoctrineResource::REGISTRY_KEY)
            ->getRepository('Litus\Entity\Users\People\Company')
            ->findAll();

        $companiesArray = array();
        foreach ($companies as $company)
            $companiesArray[$company->getId()] = $company->getName();

        return $companiesArray;
    }

    private function _getSections()
    {
        $sections = Registry::get(DoctrineResource::REGISTRY_KEY)
            ->getRepository('Litus\Entity\Br\Contracts\Section')
            ->findAll();

        $sectionsArray = array();
        foreach ($sections as $section)
            $sectionsArray[$section->getId()] = $section->getName();

        return $sectionsArray;
    }
}
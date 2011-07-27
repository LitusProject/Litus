<?php

namespace Admin\Application\Form\Acl;

use \Zend\Form\Form;
use \Zend\Form\Element\Select;
use \Zend\Form\Element\Submit;
use \Zend\Form\Element\Text;

class Add extends \Zend\Form\Form
{
    public function __construct($parentOptions, $options = null)
    {
        parent::__construct($options);

        $name = new Text('name');
        $name->setLabel('Name')
            ->setRequired();
        $this->addElement($name);

        $parent = new Select('parent');
        $parent->setLabel('Parent')
            ->setMultiOptions($parentOptions)
            ->setRequired();
        $this->addElement($parent);

        $submit = new Submit('submit');
		$submit->setLabel('Add')
            ->setAttrib('class', 'groups_add');
		$this->addElement($submit);
    }
}
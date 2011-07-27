<?php

namespace Admin\Form\Study;

use \Zend\Form\Form;
use \Zend\Form\Element\Select;
use \Zend\Form\Element\Checkbox;
use \Zend\Form\Element\Submit;
use \Zend\Form\Element\Text;

class Add extends \Zend\Form\Form
{
    public function __construct($options = null)
    {
        parent::__construct($options);

        $title = new Text('title');
        $title->setLabel('Title')
            ->setRequired();
        $this->addElement($title);

        $acronym = new Text('acronym');
        $acronym->setLabel('Acronym')
            ->setRequired();
        $this->addElement($acronym);

        $url = new Text('url');
        $url->setLabel('URL')
            ->setRequired();
        $this->addElement($url);
/*
        $phase = new Select('phase');
        $phase->setLabel('Phase')
        	->setOptions( Array(1,2,3) )
            ->setRequired();
        $this->addElement($phase);
*/
        $active = new Checkbox('active');
        $active->setLabel('Active')
            ->setRequired();
        $this->addElement($active);

        $submit = new Submit('submit');
		$submit->setLabel('Add');
		$this->addElement($submit);
    }
}

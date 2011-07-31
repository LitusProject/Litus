<?php

namespace Admin\Form\Textbook;

use Zend\Form\Decorator\HtmlTag;

use Litus\Form\Decorator\FieldDecorator;

use \Zend\Form\SubForm;
use \Zend\Form\Element\Text;
use Zend\Form\Element\Checkbox;


class AddInternal extends \Zend\Form\SubForm
{
    public function __construct($options = null)
    {
        parent::__construct($options);

        $this->setDecorators(array('FormElements', new HtmlTag(array("tag" => "span", "id" => "internal_form", "style" => "display:none"))));
        // Make sure the attributes belonging to internal articles aren't seperated when posting
        $this->setIsArray(false);

        $nrbwpages = new Text('nrbwpages');
        $nrbwpages->setLabel('Number of black and white pages')
        ->setRequired()
        ->addValidator('int')
        ->setDecorators(array(new FieldDecorator()));
        $this->addElement($nrbwpages);

        $nrcolorpages = new Text('nrcolorpages');
        $nrcolorpages->setLabel('Number of colored pages')
        ->setRequired()
        ->addValidator('int')
        ->setDecorators(array(new FieldDecorator()));
        $this->addElement($nrcolorpages);
         
        // TODO: binding when db is ready for it
        //     	$binding = new Text('binding');
        //     	$binding->setLabel('Binding')
        //     		->setRequired()
        //     		->setDecorators(array(new FieldDecorator()));
        //     	$this->addElement($binding);
         
        $official = new Checkbox('official');
        $official->setLabel('Official')
        ->setDecorators(array(new FieldDecorator()));
        $this->addElement($official);
         
        $rectoverso = new Checkbox('rectoverso');
        $rectoverso->setLabel('Recto verso')
        ->setDecorators(array(new FieldDecorator()));
        $this->addElement($rectoverso);
         
        // TODO: frontcolor when db is ready for it
        //     	$frontcolor = new Text('frontcolor');
        //     	$frontcolor->setLabel('Front page color')
        //     		->setRequired()
        //     		->setDecorators(array(new FieldDecorator()));
        //     	$this->addElement($frontcolor);


    }
}
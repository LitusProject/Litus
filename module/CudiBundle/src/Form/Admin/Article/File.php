<?php

namespace CudiBundle\Form\Admin\Article;

use CommonBundle\Component\Validator\Price as PriceValidator,
	CommonBundle\Component\Validator\Year as YearValidator
	CudiBundle\Component\Validator\UniqueArticleBarcode as UniqueArticleBarcodeValidator,
	
	CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
	CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
	CommonBundle\Component\Form\Admin\Decorator\FileDecorator,
	
	Zend\Form\Form,
	Zend\Form\Element\File as FileElement,
	Zend\Form\Element\Submit,
	Zend\Form\Element\Text,
	Zend\Form\Element\Select,
	Zend\Form\Element\Checkbox,
	Zend\Form\SubForm;

class File extends \CommonBundle\Component\Form\Admin\Form
{

    public function __construct($options = null)
    {
        parent::__construct($options);

        $this->setMethod('post');
         
        $field = new Text('description');
        $field->setLabel('Description')
			->setAttrib('size', 70)
        	->setRequired()
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);
        
        $field = new FileElement('file');
        $field->setLabel('File')
        	->setAttrib('size', 70)
        	->setRequired()
        	->setDecorators(array(new FileDecorator()));
        $this->addElement($field);
        
        $field = new Submit('submit');
        $field->setLabel('Add')
                ->setAttrib('class', 'file_add')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }
}
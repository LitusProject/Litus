<?php

namespace CudiBundle\Form\Admin\Delivery;

use CommonBundle\Component\Validator\Price as PriceValidator,
	CudiBundle\Component\Validator\ArticleBarcode as ArticleBarcodeValidator,
	
	CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
	CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
	
	Zend\Form\Form,
	Zend\Form\Element\Submit,
	Zend\Form\Element\Text,
	
	Zend\Validator\Int as IntValidator;

class Add extends \Litus\Form\Admin\Form
{

    public function __construct($options = null)
    {
        parent::__construct($options);

        $this->setMethod('post');

		$field = new Text('number');
        $field->setLabel('Number')
        	->setRequired()
        	->setDecorators(array(new FieldDecorator()))
			->addValidator(new IntValidator());
        $this->addElement($field);

		$field = new Text('stockArticle');
        $field->setLabel('Article')
        	->setRequired()
        	->setDecorators(array(new FieldDecorator()))
			->addValidator(new ArticleBarcodeValidator());
        $this->addElement($field);

        $field = new Submit('submit');
        $field->setLabel('Add')
                ->setAttrib('class', 'stock_add')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }
}
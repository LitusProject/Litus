<?php

namespace CudiBundle\Form\Admin\Order;

use CudiBundle\Component\Validator\ArticleBarcode as ArticleBarcodeValidator,

	CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
	CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
	
	Zend\Form\Form,
	Zend\Form\Element\Submit,
	Zend\Form\Element\Select,
	Zend\Form\Element\Text,
	
	Zend\Validator\Int as IntValidator;

class AddItem extends \Litus\Form\Admin\Form
{

    public function __construct($options = null)
    {
        parent::__construct($options);

        $this->setMethod('post');

		$field = new Text('number');
        $field->setLabel('Number')
        	->setRequired()
			->addValidator(new IntValidator())
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Text('stockArticle');
        $field->setLabel('Stock article')
        	->setRequired()
			->addValidator(new ArticleBarcodeValidator())
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Submit('submit');
        $field->setLabel('Add')
                ->setAttrib('class', 'stock_add')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }
}
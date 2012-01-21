<?php

namespace Admin\Form\Delivery;

use \Litus\Validator\Price as PriceValidator;
use \Litus\Validator\ArticleBarcode as ArticleBarcodeValidator;

use \Litus\Form\Admin\Decorator\ButtonDecorator;
use \Litus\Form\Admin\Decorator\FieldDecorator;

use \Zend\Form\Form;
use \Zend\Form\Element\Submit;
use \Zend\Form\Element\Text;

use \Zend\Validator\Int as IntValidator;

use \Zend\Registry;

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
<?php

namespace Admin\Form\Order;

use Litus\Validator\ArticleBarcode as ArticleBarcodeValidator;

use \Litus\Form\Admin\Decorator\ButtonDecorator;
use \Litus\Form\Admin\Decorator\FieldDecorator;

use \Zend\Form\Form;
use \Zend\Form\Element\Submit;
use \Zend\Form\Element\Select;
use \Zend\Form\Element\Text;

use \Zend\Validator\Int as IntValidator;

use \Zend\Registry;

class AddItem extends \Litus\Form\Admin\Form
{

    public function __construct($options = null)
    {
        parent::__construct($options);

        $this->setMethod('post');
		
        $field = new Text('stockArticle');
        $field->setLabel('Stock article')
        	->setRequired()
			->addValidator(new ArticleBarcodeValidator())
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

		$field = new Text('number');
        $field->setLabel('Number')
        	->setRequired()
			->addValidator(new IntValidator())
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Submit('submit');
        $field->setLabel('Add')
                ->setAttrib('class', 'stock_add')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }
}
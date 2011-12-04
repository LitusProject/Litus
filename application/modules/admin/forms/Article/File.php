<?php

namespace Admin\Form\Article;

use \Litus\Validator\Price as PriceValidator;
use \Litus\Validator\Year as YearValidator;
use \Litus\Validator\UniqueArticleBarcode as UniqueArticleBarcodeValidator;

use \Litus\Form\Admin\Decorator\ButtonDecorator;
use \Litus\Form\Admin\Decorator\FieldDecorator;
use \Litus\Form\Admin\Decorator\FileDecorator;

use \Zend\Form\Form;
use \Zend\Form\Element\File as FileElement;
use \Zend\Form\Element\Submit;
use \Zend\Form\Element\Text;
use \Zend\Form\Element\Select;
use \Zend\Form\Element\Checkbox;
use \Zend\Form\SubForm;

use \Zend\Registry;

class File extends \Litus\Form\Admin\Form
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
    }}
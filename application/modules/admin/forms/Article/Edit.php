<?php

namespace Admin\Form\Article;

use \Litus\Validator\UniqueArticleBarcode as UniqueArticleBarcodeValidator;

use \Litus\Form\Admin\Decorator\ButtonDecorator;

use Zend\Form\Element\Submit;

class Edit extends \Admin\Form\Article\Add
{

    public function __construct($options = null)
    {
        parent::__construct($options);

        $this->removeElement('submit');
        
        $this->getElement('purchase_price')
        	->setAttrib('disabled', 'disabled')
        	->clearValidators()
        	->setRequired(false);
        $this->getElement('sellprice_nomember')
        	->setAttrib('disabled', 'disabled')
        	->clearValidators()
        	->setRequired(false);
        $this->getElement('sellprice_member')
        	->setAttrib('disabled', 'disabled')
        	->clearValidators()
        	->setRequired(false);
        $this->getElement('barcode')
        	->setAttrib('disabled', 'disabled')
        	->clearValidators()
        	->setRequired(false);
        
		$field = new Submit('submit');
        $field->setLabel('Edit')
                ->setAttrib('class', 'article_edit')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }
    
    public function populate($article)
    {
    	$this->getElement('barcode')
    		->clearValidators()
        	->addValidator(new UniqueArticleBarcodeValidator(array($article->getId())));
    	
    	parent::populate($article);
    }
}
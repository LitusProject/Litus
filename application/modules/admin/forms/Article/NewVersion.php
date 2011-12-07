<?php

namespace Admin\Form\Article;

use \Litus\Validator\UniqueArticleBarcode as UniqueArticleBarcodeValidator;

use \Litus\Form\Admin\Decorator\ButtonDecorator;

use Zend\Form\Element\Submit;

class NewVersion extends \Admin\Form\Article\Add
{

    public function __construct($options = null)
    {
        parent::__construct($options);

        $this->removeElement('submit');
        
		$field = new Submit('submit');
        $field->setLabel('Add version')
                ->setAttrib('class', 'article_add')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }
    
    public function populate($article)
    {
    	parent::populate($article);
    	
   		$this->getElement('barcode')
	    	->setValue('');
    		
    }
}
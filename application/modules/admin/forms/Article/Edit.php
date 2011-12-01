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

		$submit = new Submit('submit');
        $submit->setLabel('Edit')
                ->setAttrib('class', 'textbooks_edit')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($submit);
    }
    
    public function populate($article)
    {
    	$this->getElement('barcode')
    		->clearValidators()
        	->addValidator(new UniqueArticleBarcodeValidator(array($article->getId())));
    		
    	parent::populate($article);
    }
}
<?php

namespace CudiBundle\Form\Admin\Article;

use CudiBundle\Component\Validator\UniqueArticleBarcode as UniqueArticleBarcodeValidator,

	CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
	
	Zend\Form\Element\Submit;

class NewVersion extends \CommonBundle\Component\Form\Admin\Form
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
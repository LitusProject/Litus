<?php

namespace CudiBundle\Form\Admin\Article;

use CudiBundle\Component\Validator\UniqueArticleBarcode as UniqueArticleBarcodeValidator
	
	CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
	
	Doctrine\ORM\EntityManager,
	
	Zend\Form\Element\Submit;

class Edit extends \CommonBundle\Component\Form\Admin\Form
{
	
	/**
	 * @var \Doctrine\ORM\EntityManager The EntityManager instance
	 */
	private $_entityManager = null;
	
    public function __construct(EntityManager $entityManager, $options = null)
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
        	->addValidator(new UniqueArticleBarcodeValidator($this->_entityManager, array($article->getId())));
    	
    	parent::populate($article);
    }
}
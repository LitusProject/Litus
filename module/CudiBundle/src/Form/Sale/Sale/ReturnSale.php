<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
 
namespace CudiBundle\Form\Sale\Sale;

use CommonBundle\Component\Validator\ValidUsername as UsernameValidator,
	CommonBundle\Component\Form\Bootstrap\Element\Reset,
	CommonBundle\Component\Form\Bootstrap\Element\Submit,
	CommonBundle\Component\Form\Bootstrap\Element\Text,
	CudiBundle\Component\Validator\ArticleBarcode as ArticleBarcodeValidator,
	Doctrine\ORM\EntityManager,
	Zend\Form\Element\Hidden,
	Zend\Validator\Int as IntValidator;
	
/**
 * Return Sale
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ReturnSale extends \CommonBundle\Component\Form\Bootstrap\Form
{
    public function __construct(EntityManager $entityManager, $opts = null )
    {
        parent::__construct($opts);
        
        $field = new Hidden('person_id');
        $field->setRequired()
            ->addValidator(new IntValidator())
            ->setAttrib('id', 'personId')
            ->clearDecorators()
            ->setDecorators(array('ViewHelper'));
        $this->addElement($field);
         
        $field = new Text('person');
        $field->setLabel('Person')
			->setAttrib('placeholder', 'Student')
        	->setAttrib('style', 'width: 400px;')
        	->setAttrib('id', 'personSearch')
        	->setAttrib('autocomplete', 'off')
        	->setAttrib('data-provide', 'typeahead')
        	->setRequired();
        $this->addElement($field);
        
        $field = new Text('article');
        $field->setLabel('Article')
            ->setRequired()
        	->setAttrib('autocomplete', 'off')
			->setAttrib('placeholder', 'Article Barcode')
        	->addValidator(new ArticleBarcodeValidator($entityManager));
        $this->addElement($field);
      	
        $field = new Submit('submit');
        $field->setLabel('Return')
			->setAttrib('autocomplete', 'off')
        	->setAttrib('id', 'signin');
        $this->addElement($field);
        
        $field = new Reset('cancel');
        $field->setLabel('Cancel');
        $this->addElement($field);
        
        $this->setActionsGroup(array('submit', 'cancel'));
    }
}

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
 
namespace CudiBundle\Form\Admin\Article;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
	CudiBundle\Component\Validator\UniqueArticleBarcode as UniqueArticleBarcodeValidator,
	CudiBundle\Entity\Article,
	Doctrine\ORM\EntityManager,
	Zend\Form\Element\Submit;

class Edit extends \CudiBundle\Form\Admin\Article\Add
{
	
    public function __construct(EntityManager $entityManager, Article $article, $options = null)
    {
        parent::__construct($entityManager, $options);

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
        	->addValidator(new UniqueArticleBarcodeValidator($entityManager, array($article->getId())))
        	->setRequired(false);
        
		$field = new Submit('submit');
        $field->setLabel('Edit')
                ->setAttrib('class', 'article_edit')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
        
        $this->populate($article);
    }
}
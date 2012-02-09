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
    
    public function populate(Article $article)
    {
    	parent::populate($article);
    	
   		$this->getElement('barcode')
	    	->setValue('');
    		
    }
}
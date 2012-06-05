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
 
namespace CudiBundle\Form\Admin\Stock\Deliveries;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
	CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
	CudiBundle\Component\Validator\ArticleBarcode as ArticleBarcodeValidator,	
	Doctrine\ORM\EntityManager,
	Zend\Form\Element\Submit,
	Zend\Form\Element\TextArea;

/**
 * Return to supplier (inverse of delivery)
 *
 * (named so because php complains when 'Return' is used)
 *
 * @author Alan Szepieniec <alan.szepieniec@litus.cc> (copied & adapted from Kristof's Add.php)
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Retour extends \CudiBundle\Form\Admin\Stock\Deliveries\Add
{
    public function __construct(EntityManager $entityManager, $options = null)
    {
        parent::__construct($entityManager, $options);
        
        $submit = $this->getElement('submit');
        $this->removeElement('submit');
        
        $field = new TextArea('comment');
        $field->setLabel('Comment')
        	->setRequired()
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);
        
        $this->addElement($submit);
    }
}


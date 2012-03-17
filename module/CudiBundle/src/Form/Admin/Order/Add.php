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
 
namespace CudiBundle\Form\Admin\Order;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
	CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
	CudiBundle\Component\Validator\ArticleBarcode as ArticleBarcodeValidator,
	Doctrine\ORM\EntityManager,
	Zend\Form\Element\Submit,
	Zend\Form\Element\Select,
	Zend\Form\Element\Text,
	Zend\Form\Form,
	Zend\Validator\Int as IntValidator;

class Add extends \CommonBundle\Component\Form\Admin\Form
{

    public function __construct(EntityManager $entityManager, $options = null)
    {
        parent::__construct($options);

        $field = new Text('stockArticle');
        $field->setLabel('Stock Article')
            ->setAttrib('class', 'disableEnter')
        	->setRequired()
			->addValidator(new ArticleBarcodeValidator($entityManager))
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);
        
        $field = new Text('number');
        $field->setLabel('Number')
        	->setRequired()
        	->addValidator(new IntValidator())
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Submit('submit');
        $field->setLabel('Add')
                ->setAttrib('class', 'stock_add')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }
}
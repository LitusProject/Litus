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
 
namespace CudiBundle\Form\Admin\Stock;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
	CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
	CudiBundle\Entity\Stock\StockItem,
	Zend\Form\Element\Submit,
	Zend\Form\Element\Text,
	Zend\Validator\Int as IntValidator;
	
class Update extends \CommonBundle\Component\Form\Admin\Form
{

    public function __construct(StockItem $item, $options = null)
    {
        parent::__construct($options);
		
        $field = new Text('number');
        $field->setLabel('Number')
        	->setRequired()
			->addValidator(new IntValidator())
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Submit('updateStock');
        $field->setLabel('Update')
                ->setAttrib('class', 'stock_edit')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
        
        $this->populate(array(
        		'number' => $item->getNumberInStock()
        	)
        );
    }
}
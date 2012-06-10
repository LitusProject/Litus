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
 
namespace CommonBundle\Form\Admin\Address;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
	CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
	CommonBundle\Entity\General\Address,
	Zend\Form\Element\Select,
	Zend\Form\Element\Text,
	Zend\Validator\Alpha as AlphaValidator,
	Zend\Validator\Digits as DigitsValidator;

/**
 * Add Address
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\SubForm
{
	/**
	 * @param mixed $opts The form's options
	 */
    public function __construct($prefix = '', $opts = null)
    {
        parent::__construct($opts);
        
        $prefix = '' == $prefix ? '' : $prefix . '_';
		
		$field = new Text($prefix . 'address_street');
		$field->setLabel('Street')
			->setRequired()
		    ->setDecorators(array(new FieldDecorator()))
		    ->addValidator(new AlphaValidator());
		$this->addElement($field);
		
		$field = new Text($prefix . 'address_number');
		$field->setLabel('Number')
			->setRequired()
			->setAttrib('size', 5)
		    ->setDecorators(array(new FieldDecorator()))
		    ->addValidator(new DigitsValidator());
		$this->addElement($field);
		
		$field = new Text($prefix . 'address_postal');
		$field->setLabel('Postal Code')
			->setRequired()
			->setAttrib('size', 10)
		    ->setDecorators(array(new FieldDecorator()))
		    ->addValidator(new DigitsValidator());
		$this->addElement($field);
		
		$field = new Text($prefix . 'address_city');
		$field->setLabel('City')
			->setRequired()
		    ->setDecorators(array(new FieldDecorator()))
		    ->addValidator(new AlphaValidator());
		$this->addElement($field);
		
		$field = new Select($prefix . 'address_country');
		$field->setLabel('Country')
			->setRequired()
			->setMultiOptions(Address::$countries)
			->setDecorators(array(new FieldDecorator()));
		$this->addElement($field);
		
		$this->populate(
			array(
				'address_country' => 'BE'
			)
		);
    }
}

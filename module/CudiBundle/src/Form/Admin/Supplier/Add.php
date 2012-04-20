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
 
namespace CudiBundle\Form\Admin\Supplier;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
	CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
	CommonBundle\Component\Validator\PhoneNumber as PhoneNumberValidator,
	CudiBundle\Component\Validator\Supplier as SupplierValidator,
	Doctrine\ORM\EntityManager,
	Zend\Form\Element\Submit,
	Zend\Form\Element\Text;

/**
 * Add Supplier
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    public function __construct(EntityManager $entityManager, $options = null)
    {
        parent::__construct($options);
         
		$field = new Text('name');
        $field->setLabel('Name')
        	->setRequired()
			->addValidator(new SupplierValidator($entityManager))
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

		$field = new Text('phone_number');
        $field->setLabel('Phone Number')
        	->setRequired()
        	->setAttrib('placeholder', '+CCAAANNNNNN')
			->addValidator(new PhoneNumberValidator())
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

		$field = new Text('address');
        $field->setLabel('Address')
        	->setRequired()
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);
        
        $field = new Text('vat');
        $field->setLabel('VAT')
        	->setRequired()
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Submit('submit');
        $field->setLabel('Add')
                ->setAttrib('class', 'supplier_add')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);

    }
}
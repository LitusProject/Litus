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
    CudiBundle\Entity\Supplier,
	Doctrine\ORM\EntityManager,
	Zend\Form\Element\Submit,
	Zend\Form\Element\Text;

class Edit extends \CudiBundle\Form\Admin\Supplier\Add
{

    public function __construct(EntityManager $entityManager, Supplier $supplier, $options = null)
    {
        parent::__construct($entityManager, $options);
         
        $this->removeElement('submit');
        
        $this->getElement('name')
        	->setAttrib('disabled', 'disabled')
        	->clearValidators()
        	->setRequired(false);
        
        $field = new Submit('submit');
        $field->setLabel('Edit')
                ->setAttrib('class', 'supplier_edit')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
        
        $this->populateFromSupplier($supplier);
    }
    
    public function populateFromSupplier(Supplier $supplier)
    {
        $this->populate(array(
        	'name' => $supplier->getName(),
        	'phone_number' => $supplier->getPhoneNumber(),
        	'address' => $supplier->getAddress(),
        	'vat' => $supplier->getVATNumber(),
        ));
    }
}
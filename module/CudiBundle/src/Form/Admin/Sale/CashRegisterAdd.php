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
 
namespace CudiBundle\Form\Admin\Sale;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
	CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
	CommonBundle\Component\Validator\Price as PriceValidator,
	Doctrine\ORM\EntityManager,
	Zend\Form\Element\Hidden,
	Zend\Form\Element\Submit,
	Zend\Form\Element\Text,
	Zend\Form\Element\Textarea,
	Zend\Form\Form,
	Zend\Validator\Int as IntValidator;

class CashRegisterAdd extends \CommonBundle\Component\Form\Admin\Form
{
    public function __construct(EntityManager $entityManager, $options = null )
    {
        parent::__construct($options);

        $units = $entityManager
            ->getRepository('CommonBundle\Entity\General\Bank\MoneyUnit')
            ->findAll();
        
		foreach($units as $unit) {
			$field = new Text('unit_' . $unit->getId());
	        $field->setLabel('&euro; ' . number_format($unit->getUnit() / 100, 2))
	            ->setRequired()
				->setValue(0)
				->addValidator(new IntValidator())
	            ->setDecorators(array(new FieldDecorator()));
	        $this->addElement($field);
		}
		
		$devices = $entityManager
            ->getRepository('CommonBundle\Entity\General\Bank\BankDevice')
            ->findAll();
        
		foreach($devices as $device) {
			$field = new Text('device_' . $device->getId());
	        $field->setLabel($device->getName())
	            ->setRequired()
				->setValue(0)
				->addValidator(new PriceValidator())
	            ->setDecorators(array(new FieldDecorator()));
	        $this->addElement($field);
		}

        $field = new Submit('submit');
        $field->setLabel('Add')
            ->setAttrib('class', 'sale_add')
            ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }
}

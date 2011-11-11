<?php

namespace Admin\Form\Sale;

use \Litus\Form\Admin\Decorator\ButtonDecorator;
use \Litus\Form\Admin\Decorator\FieldDecorator;
use \Litus\Application\Resource\Doctrine as DoctrineResource;
use \Litus\Validator\Price as PriceValidator;

use \Zend\Form\Form;
use \Zend\Form\Element\Hidden;
use \Zend\Form\Element\Submit;
use \Zend\Form\Element\Text;
use \Zend\Form\Element\Textarea;
use \Zend\Validator\Int as IntValidator;
use \Zend\Registry;

class CashRegisterAdd extends \Litus\Form\Admin\Form
{
    public function __construct($options = null )
    {
        parent::__construct($options);

        $units = Registry::get(DoctrineResource::REGISTRY_KEY)
            ->getRepository('Litus\Entity\General\Bank\MoneyUnit')
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
		
		$devices = Registry::get(DoctrineResource::REGISTRY_KEY)
            ->getRepository('Litus\Entity\General\Bank\BankDevice')
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

<?php

namespace CudiBundle\Form\Admin\Sale;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
	CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
	
	Doctrine\ORM\EntityManager,
	
	CommonBundle\Component\Validator\Price as PriceValidator,
	
	Zend\Form\Form,
	Zend\Form\Element\Submit,
	
	Zend\Validator\Int as IntValidator;
	
class CashRegisterEdit extends CashRegisterAdd
{
    public function __construct($options = null )
    {
        parent::__construct($options);

		$this->removeElement('submit');

        $field = new Submit('submit');
        $field->setLabel('Edit')
            ->setAttrib('class', 'sale_edit')
            ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }

	public function populate($data)
	{
		$array = array();
		
		foreach($data->getBankDeviceAmounts() as $amount)
			$array['device_' . $amount->getDevice()->getId()] = $amount->getAmount() / 100;
        
		foreach($data->getMoneyUnitAmounts() as $amount)
			$array['unit_' . $amount->getUnit()->getId()] = $amount->getAmount();
		
		parent::populate($array);
	}
}

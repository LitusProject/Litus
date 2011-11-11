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

class CashRegisterEdit extends \Admin\Form\Sale\CashRegisterAdd
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

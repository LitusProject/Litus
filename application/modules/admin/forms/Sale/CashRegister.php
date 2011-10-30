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
use \Zend\Validator;
use \Zend\Registry;

class CashRegister extends \Litus\Form\Admin\Form
{
    public function __construct($options = null )
    {
        parent::__construct($options);

        $units = Registry::get('EntityManager')->getRepository('Litus\Entity\Cudi\Sales\MoneyUnit')->findAll();
		foreach($units as $unit) {
			$field = new Text('unit_'.$unit->getId());
	        $field->setLabel('&euro; '.number_format($unit->getUnit()/100, 2))
	            ->setRequired()
				->setValue(0)
				->addValidator(new \Zend\Validator\Int())
	            ->setDecorators(array(new FieldDecorator()));
	        $this->addElement($field);
		}

        $field = new Text('Bank_Device_1');
        $field->setLabel('Bank Device 1')
            ->setRequired()
			->setValue(0)
            ->addValidator( new PriceValidator() )
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Text('Bank_Device_2');
        $field->setLabel('Bank Device 2')
            ->setRequired()
			->setValue(0)
            ->addValidator( new PriceValidator() )
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);


        $field = new Submit('submit');
        $field->setLabel('Submit')
            ->setAttrib('class', 'sale_add')
            ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }

	public function populate($data)
	{
		$array = array(
			'Bank_Device_1' => $data->getAmountBank1()/100,
			'Bank_Device_2' => $data->getAmountBank2()/100
		);
		foreach($data->getNumberMoneyUnits() as $number)
			$array['unit_'.$number->getUnit()->getId()] = $number->getNumber();
		
		parent::populate($array);
	}
}

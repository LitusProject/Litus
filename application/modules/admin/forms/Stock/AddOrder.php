<?php

namespace Admin\Form\Stock;

use \Litus\Validator\Price as PriceValidator;

use \Litus\Form\Admin\Decorator\ButtonDecorator;
use \Litus\Form\Admin\Decorator\FieldDecorator;

use \Zend\Form\Form;
use \Zend\Form\Element\Submit;
use \Zend\Form\Element\Select;
use \Zend\Form\Element\Text;

use \Zend\Registry;

class AddOrder extends \Litus\Form\Admin\Form
{

    public function __construct($options = null)
    {
        parent::__construct($options);

        $this->setMethod('post');
		
        $field = new Select('supplier');
        $field->setLabel('Supplier')
        	->setRequired()
			->setMultiOptions($this->_getSuppliers())
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

		$field = new Text('price');
        $field->setLabel('Price')
        	->setRequired()
        	->setDecorators(array(new FieldDecorator()))
        	->addValidator(new PriceValidator());
        $this->addElement($field);

        $field = new Submit('submit');
        $field->setLabel('Add')
                ->setAttrib('class', 'stock_add')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }

	private function _getSuppliers()
	{
		$suppliers = Registry::get('EntityManager')
            ->getRepository('Litus\Entity\Cudi\Supplier')
			->findAll();
		$supplierOptions = array();
		foreach($suppliers as $item)
			$supplierOptions[$item->getId()] = $item->getName();
		
		return $supplierOptions;
	}
}
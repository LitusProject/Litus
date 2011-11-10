<?php

namespace Admin\Form\Order;

use \Litus\Form\Admin\Decorator\ButtonDecorator;

use Zend\Form\Element\Submit;

class Edit extends \Admin\Form\Order\Add
{

    public function __construct($options = null)
    {
        parent::__construct($options);
		
        $this->removeElement('submit');

		$submit = new Submit('submit');
        $submit->setLabel('Edit')
                ->setAttrib('class', 'stock_edit')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($submit);
    }

	public function populate($order)
	{
		$data = array(
			'supplier' => $order->getSupplier()->getId()
		);
		
		parent::populate($data);
	}

}
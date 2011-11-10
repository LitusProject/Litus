<?php

namespace Admin\Form\Order;

use \Zend\Form\Form;

class AddDirect extends \Admin\Form\Order\AddItem
{

    public function __construct($options = null)
    {
        parent::__construct($options);

        $this->removeElement('stockArticle');
		$this->getElement('submit')->setName('addOrder');
    }
}
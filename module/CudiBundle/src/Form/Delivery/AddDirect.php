<?php

namespace Admin\Form\Delivery;

use \Zend\Form\Form;

class AddDirect extends \Admin\Form\Delivery\Add
{

    public function __construct($options = null)
    {
        parent::__construct($options);

        $this->removeElement('stockArticle');
		$this->getElement('submit')->setName('addDelivery');
    }
}
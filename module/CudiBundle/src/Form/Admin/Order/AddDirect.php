<?php

namespace CudiBundle\Form\Admin\Order;

use Zend\Form\Form;

class AddDirect extends \CommonBundle\Component\Form\Admin\Form
{

    public function __construct($options = null)
    {
        parent::__construct($options);

        $this->removeElement('stockArticle');
		$this->getElement('submit')->setName('addOrder');
    }
}
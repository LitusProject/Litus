<?php

namespace Litus\Form\Bootstrap;

class Form extends \Zend\Form\Form
{
    public function __construct($options)
    {
        parent::__construct($options = null);

        $this->setMethod('post');

        $this->setDecorators(array());
    }
}
<?php

namespace Litus\Form;

class Form extends \Zend\Form\Form
{
    public function __construct($options)
    {
        parent::__construct($options = null);

        $this->setMethod('post');
        
        $this->addDecorator(
            'HtmlTag',
            array(
                 'tag' => 'div',
                 'class' => 'form'
            )
        );
    }
}
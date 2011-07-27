<?php

namespace Litus\Form\Decorator;

use Zend\Form\Decorator\Errors;

use Zend\Form\Decorator\ViewHelper;

use Zend\Form\Decorator\Label;

use Zend\Form\Decorator\AbstractDecorator;

/**
* This decorator combines all decorators needed to decorate a field with a label.
*/
class FieldDecorator extends AbstractDecorator
{
    
    public function render($content)
    {	
    	$viewHelper = new ViewHelper();
    	$viewHelper->setElement($this->getElement());
    	$content = $viewHelper->render($content);
    	
    	$divSpanWrapper = new DivSpanWrapper();
    	$divSpanWrapper->setElement($this->getElement());
    	$content = $divSpanWrapper->render($content);
    	
    	$error = new Errors();
    	$error->setElement($this->getElement());
    	$error->setOption('placement', 'prepend');
    	$content = $error->render($content);
    	
    	return $content;
    }
}
<?php

namespace Litus\Form\Decorator;

use Zend\Form\Decorator\Label;

use Zend\Form\Decorator\AbstractDecorator;

/**
* This decorator will be used to decorate our form fields with div and span tags. It uses the label decorator internally.
*/
class DivSpanWrapper extends AbstractDecorator
{
    
    public function render($content)
    {
    	$elementName = $this->getElement()->getName();

    	$labelDecorator = new Label();
    	$labelDecorator->setElement($this->getElement());
    	$label = $labelDecorator->render('');
    	
    	return '<div class="row"> <span class="label">' . $label . '</span> <span class="field">' . $content . '</span></div>';    	
    }
}
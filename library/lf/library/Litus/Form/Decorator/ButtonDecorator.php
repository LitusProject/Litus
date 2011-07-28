<?php

namespace Litus\Form\Decorator;

use \Zend\Form\Decorator\ViewHelper;

/**
* This decorator combines all decorators needed to decorate a field with a label.
*/
class ButtonDecorator extends \Zend\Form\Decorator\AbstractDecorator
{
    public function render($content)
    {
    	$viewHelper = new ViewHelper();
    	$viewHelper->setElement($this->getElement());
    	$content = $viewHelper->render($content);

        $this->getElement()->setLabel('');

    	$divSpanWrapper = new DivSpanWrapper();
    	$divSpanWrapper->setElement($this->getElement());
    	$content = $divSpanWrapper->render($content);

    	return $content;
    }
}
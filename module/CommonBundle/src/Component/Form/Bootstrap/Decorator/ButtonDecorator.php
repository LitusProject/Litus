<?php

namespace Litus\Form\Bootstrap\Decorator;

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

    	return '<div class="actions">' . $viewHelper->render($content) . '</div>';
    }
}
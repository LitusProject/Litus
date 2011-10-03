<?php

namespace Litus\Form\Bootstrap\Decorator;

use \Zend\Form\Decorator\Errors;
use \Zend\Form\Decorator\ViewHelper;

/**
 * This decorator combines all decorators needed to decorate a field with a label.
 */
class FieldDecorator extends \Zend\Form\Decorator\AbstractDecorator
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
        $error->setOption('placement', 'append');
        $content = $error->render($content);

        return $content;
    }
}
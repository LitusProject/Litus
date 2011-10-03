<?php

namespace Litus\Form\Bootstrap\Decorator;

use Zend\Form\Decorator\AbstractDecorator;
use Zend\Form\Decorator\Label;

/**
 * This decorator will be used to decorate our form fields with div and span tags.
 * It uses the label decorator internally.
 */
class DivSpanWrapper extends AbstractDecorator
{
    public function render($content)
    {
        $labelDecorator = new Label();
        $labelDecorator->setElement($this->getElement());
        $label = $labelDecorator->render('');

        return '<div class="clearfix">' . $label . '<div class="input">' . $content . '</div></div>';
    }
}
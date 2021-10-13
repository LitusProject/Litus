<?php

namespace CommonBundle\Component\Form\Bootstrap\Element;

use CommonBundle\Component\Form\ElementTrait;

/**
 * Button form element
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Button extends \Laminas\Form\Element\Button implements \CommonBundle\Component\Form\ElementInterface
{
    use ElementTrait;

    public function init()
    {
        $this->addClass('btn btn-primary');
    }
}

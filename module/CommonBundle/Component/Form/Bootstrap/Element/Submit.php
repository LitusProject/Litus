<?php

namespace CommonBundle\Component\Form\Bootstrap\Element;

use CommonBundle\Component\Form\ElementTrait;

/**
 * Submit form element
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Submit extends \Laminas\Form\Element\Submit implements \CommonBundle\Component\Form\ElementInterface
{
    use ElementTrait;

    public function init()
    {
        $this->addClass('btn btn-primary button');
    }
}

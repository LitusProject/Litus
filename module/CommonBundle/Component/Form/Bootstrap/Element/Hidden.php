<?php

namespace CommonBundle\Component\Form\Bootstrap\Element;

use CommonBundle\Component\Form\ElementTrait;

/**
 * Hidden form element
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Hidden extends \Laminas\Form\Element\Hidden implements \CommonBundle\Component\Form\ElementInterface
{
    use ElementTrait;
}

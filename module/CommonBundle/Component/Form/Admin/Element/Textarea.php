<?php

namespace CommonBundle\Component\Form\Admin\Element;

use CommonBundle\Component\Form\ElementTrait;

/**
 * Textarea form element
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Textarea extends \Laminas\Form\Element\Textarea implements \CommonBundle\Component\Form\ElementInterface
{
    use ElementTrait;
}

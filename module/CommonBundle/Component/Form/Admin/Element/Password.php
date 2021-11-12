<?php

namespace CommonBundle\Component\Form\Admin\Element;

use CommonBundle\Component\Form\ElementTrait;

/**
 * Password form element
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Password extends \Laminas\Form\Element\Password implements \CommonBundle\Component\Form\ElementInterface
{
    use ElementTrait;
}

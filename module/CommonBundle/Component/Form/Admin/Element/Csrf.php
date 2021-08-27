<?php

namespace CommonBundle\Component\Form\Admin\Element;

use CommonBundle\Component\Form\ElementTrait;

/**
 * CSRF form element
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Csrf extends \Laminas\Form\Element\Csrf implements \CommonBundle\Component\Form\ElementInterface
{
    use ElementTrait;
}

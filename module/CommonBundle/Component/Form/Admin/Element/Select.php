<?php

namespace CommonBundle\Component\Form\Admin\Element;

use CommonBundle\Component\Form\ElementTrait;

/**
 * Select form element
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Select extends \Laminas\Form\Element\Select implements \CommonBundle\Component\Form\ElementInterface
{
    use ElementTrait;
}

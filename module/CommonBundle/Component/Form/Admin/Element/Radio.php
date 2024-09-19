<?php

namespace CommonBundle\Component\Form\Admin\Element;

use CommonBundle\Component\Form\ElementTrait;

/**
 * Radio form element
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Radio extends \Laminas\Form\Element\Radio implements \CommonBundle\Component\Form\ElementInterface
{
    use ElementTrait;

    public function init()
    {
        $this->setLabelAttributes(
            array(
                'class' => 'radio',
            )
        );
    }
}

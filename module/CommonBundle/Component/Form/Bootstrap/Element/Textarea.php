<?php

namespace CommonBundle\Component\Form\Bootstrap\Element;

use CommonBundle\Component\Form\ElementTrait;

/**
 * Textarea form element
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Textarea extends \Laminas\Form\Element\Textarea implements \CommonBundle\Component\Form\ElementInterface
{
    use ElementTrait;

    public function init()
    {
        if (!$this->hasAttribute('rows')) {
            $this->setAttribute('rows', 10);
        }

        $this->addClass('form-control');
        $this->setLabelAttributes(
            array(
                'class' => 'col-sm-2 control-label',
            )
        );
    }
}

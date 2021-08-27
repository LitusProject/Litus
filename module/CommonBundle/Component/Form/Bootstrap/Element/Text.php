<?php

namespace CommonBundle\Component\Form\Bootstrap\Element;

use CommonBundle\Component\Form\ElementTrait;

/**
 * Text form element
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Text extends \Laminas\Form\Element\Text implements \CommonBundle\Component\Form\ElementInterface
{
    use ElementTrait;

    public function init()
    {
        $this->addClass('form-control');
        $this->setLabelAttributes(
            array(
                'class' => 'col-sm-2 control-label',
            )
        );
    }
}

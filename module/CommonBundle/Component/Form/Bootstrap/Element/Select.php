<?php

namespace CommonBundle\Component\Form\Bootstrap\Element;

use CommonBundle\Component\Form\ElementTrait;

/**
 * Select form element
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Select extends \Laminas\Form\Element\Select implements \CommonBundle\Component\Form\ElementInterface
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

<?php

namespace CommonBundle\Component\Form\Bootstrap\Element;

use CommonBundle\Component\Form\ElementTrait;

/**
 * Reset form element
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Reset extends \Laminas\Form\Element\Submit implements \CommonBundle\Component\Form\ElementInterface
{
    use ElementTrait;

    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'type' => 'reset',
    );

    public function init()
    {
        $this->addClass('btn btn-default');
    }
}

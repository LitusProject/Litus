<?php

namespace CommonBundle\Component\Form\Admin;

use CommonBundle\Component\Form\Admin\Element\Csrf;

/**
 * Extending Laminas's form component, so that our forms look the way we want
 * them to.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
abstract class Form extends \CommonBundle\Component\Form\Form
{
    /**
     * @param string|integer|null $name Optional name for the element
     */
    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->setAttribute('class', 'form');

        $this->add(
            new Csrf('csrf')
        );
    }
}

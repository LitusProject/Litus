<?php

namespace CommonBundle\Component\Form\Bootstrap;

use Laminas\Form\Element\Csrf;

/**
 * Extending Zend's form component, so that our forms look the way we want
 * them to.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
abstract class Form extends \CommonBundle\Component\Form\Form
{
    /**
     * @var boolean Whether or not to show the form-actions div
     */
    private $displayFormActions;

    /**
     * @param string|integer|null $name               Optional name for the element
     * @param boolean             $horizontal         Whether to display the form horizontically or vertically
     * @param boolean             $displayFormActions Whether or not to show the form-actions div
     */
    public function __construct($name = null, $horizontal = true, $displayFormActions = true)
    {
        parent::__construct($name);

        $this->displayFormActions = $displayFormActions;

        if ($horizontal) {
            $this->setAttribute('class', 'form-horizontal');
        }

        $this->add(
            new Csrf('csrf')
        );
    }

    /**
     * Whether or not to show the form-actions div
     *
     * @return boolean
     */
    public function getDisplayFormActions()
    {
        return $this->displayFormActions;
    }
}

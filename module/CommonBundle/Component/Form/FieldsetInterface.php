<?php

namespace CommonBundle\Component\Form;

use Laminas\InputFilter\InputFilterProviderInterface;

/**
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
interface FieldsetInterface extends \Laminas\Form\FieldsetInterface, ElementInterface, InputFilterProviderInterface
{
    /**
     * @return string Returns 'fieldset', 'div' or 'none'
     */
    public function showAs();
}

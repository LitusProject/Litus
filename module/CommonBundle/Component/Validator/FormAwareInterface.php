<?php

namespace CommonBundle\Component\Validator;

use CommonBundle\Component\Form\Form;

/**
 * Extending Zend's validator component, so that we can inject a form
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
interface FormAwareInterface
{
    /**
     * @param  Form $form
     * @return self
     */
    public function setForm(Form $form);
}

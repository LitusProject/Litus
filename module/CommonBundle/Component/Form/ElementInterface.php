<?php

namespace CommonBundle\Component\Form;

use Laminas\Form\FormInterface;

/**
 * Extending Laminas's form element component, so that our forms look the way we
 * want them to.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
interface ElementInterface extends \Laminas\Form\ElementInterface, \Laminas\InputFilter\InputProviderInterface, \Laminas\Form\ElementPrepareAwareInterface
{
    /**
     * Specifies whether this element is a required field. Also sets the HTML5
     * 'required' attribute.
     *
     * @param  boolean $flag
     * @return self
     */
    public function setRequired($flag = true);

    /**
     * @return boolean
     */
    public function isRequired();

    /**
     * @param  string $class The class(es) to add
     * @return self
     */
    public function addClass($class);

    /**
     * Prepare the form element (mostly used for rendering purposes)
     *
     * @param  FormInterface $form
     * @return mixed
     */
    public function prepareElement(FormInterface $form);
}

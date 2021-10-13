<?php

namespace CommonBundle\Component\Form\Admin\Element;

use CommonBundle\Component\Form\ElementTrait;
use Laminas\Form\FormInterface;

/**
 * File form element
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class File extends \Laminas\Form\Element\File implements \CommonBundle\Component\Form\ElementInterface
{
    use ElementTrait {
        ElementTrait::getInputSpecification as getTraitInputSpecification;
        ElementTrait::prepareElement as traitPrepareElement;
    }

    public function getInputSpecification()
    {
        $specification = $this->getTraitInputSpecification();
        $specification['type'] = 'Laminas\InputFilter\FileInput';

        return $specification;
    }

    /**
     * Prepare the form element (mostly used for rendering purposes)
     *
     * @param  FormInterface $form
     * @return mixed
     */
    public function prepareElement(FormInterface $form)
    {
        $this->traitPrepareElement($form);

        return parent::prepareElement($form);
    }
}

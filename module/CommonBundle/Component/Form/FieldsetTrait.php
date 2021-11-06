<?php

namespace CommonBundle\Component\Form;

use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\InputFilter\InputProviderInterface;

/**
 * FieldsetTrait
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
trait FieldsetTrait
{
    /**
     * @return array
     */
    public function getInputFilterSpecification()
    {
        $spec = array(
            'type' => 'inputfilter',
        );

        $elements = array_merge($this->elements, $this->fieldsets);

        foreach ($elements as $name => $elementOrFieldset) {
            if ($elementOrFieldset instanceof InputFilterProviderInterface) {
                $spec[$name] = $elementOrFieldset->getInputFilterSpecification();
            } elseif ($elementOrFieldset instanceof InputProviderInterface) {
                $spec[$name] = $elementOrFieldset->getInputSpecification();
            }
        }

        return $spec;
    }

    /**
     * @param  boolean $required
     * @return self
     */
    public function setRequired($required = true)
    {
        foreach ($this->elements as $elementOrFieldset) {
            if ($elementOrFieldset instanceof ElementInterface) {
                $elementOrFieldset->setRequired($required);
            }
        }

        return $this->setElementRequired($required);
    }
}

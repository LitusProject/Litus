<?php

namespace CommonBundle\Component\InputFilter;

class Factory extends \Laminas\InputFilter\Factory
{
    /**
     * {@inheritdoc}
     */
    public function createInput($inputSpecification)
    {
        if (is_array($inputSpecification)
            && isset($inputSpecification['type'])
            && is_array($inputSpecification['type'])
        ) {
            return $this->createInputFilter($inputSpecification);
        }

        return parent::createInput($inputSpecification);
    }

    /**
     * {@inheritdoc}
     */
    public function createInputFilter($inputFilterSpecification)
    {
        if (!is_array($inputFilterSpecification)
            || !isset($inputFilterSpecification['type'])
            || !is_array($inputFilterSpecification['type'])
        ) {
            return parent::createInputFilter($inputFilterSpecification);
        }

        $typeInput = $inputFilterSpecification['type'];
        $inputFilterSpecification['type'] = 'inputfilter';

        $inputFilter = parent::createInputFilter($inputFilterSpecification);

        $inputFilter->add(
            $this->createInput($typeInput),
            'type'
        );

        return $inputFilter;
    }
}

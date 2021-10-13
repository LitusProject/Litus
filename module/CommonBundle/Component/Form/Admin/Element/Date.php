<?php

namespace CommonBundle\Component\Form\Admin\Element;

/**
 * A date picker element
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Date extends \CommonBundle\Component\Form\Admin\Element\Text
{
    public function init()
    {
        parent::init();

        $this->setAttribute('placeholder', 'dd/mm/yyyy')
            ->setAttribute('data-datepicker', true);
    }

    public function getInputSpecification()
    {
        return array_merge_recursive(
            array(
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'date',
                        'options' => array(
                            'format' => 'd/m/Y',
                        ),
                    ),
                ),
            ),
            parent::getInputSpecification()
        );
    }
}

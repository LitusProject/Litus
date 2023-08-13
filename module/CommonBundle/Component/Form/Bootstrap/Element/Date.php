<?php

namespace CommonBundle\Component\Form\Bootstrap\Element;

/**
 * Date form element
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Date extends \CommonBundle\Component\Form\Bootstrap\Element\Text
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

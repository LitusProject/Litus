<?php

namespace CommonBundle\Component\Form\Admin\Element;

/**
 * Time form element
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Time extends \CommonBundle\Component\Form\Admin\Element\Text
{
    public function init()
    {
        parent::init();

        $this->setAttribute('placeholder', 'hh:mm')
            ->setAttribute('data-timepicker', true);
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
                            'format' => 'H:i',
                        ),
                    ),
                ),
            ),
            parent::getInputSpecification()
        );
    }
}

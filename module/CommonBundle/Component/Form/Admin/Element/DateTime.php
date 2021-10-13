<?php

namespace CommonBundle\Component\Form\Admin\Element;

/**
 * DateTime form element
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class DateTime extends \CommonBundle\Component\Form\Admin\Element\Text
{
    public function init()
    {
        parent::init();

        $this->setAttribute('placeholder', 'dd/mm/yyyy hh:mm')
            ->setAttribute('data-datepicker', true)
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
                            'format' => 'd/m/Y H:i',
                        ),
                    ),
                ),
            ),
            parent::getInputSpecification()
        );
    }
}

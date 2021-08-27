<?php

namespace ShiftBundle\Form\RegistrationShift\Search;

use DateTime;

/**
 * Search Date
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Date extends \CommonBundle\Component\Form\Bootstrap\Form
{
    public function __construct($name = null)
    {
        parent::__construct($name, false, false);
    }

    public function init()
    {
        parent::init();

        $now = new DateTime();

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'date',
                'label'      => 'Date',
                'value'      => $now->format('d/m/Y'),
                'attributes' => array(
                    'placeholder' => 'dd/mm/yyyy',
                ),
                'options' => array(
                    'input' => array(
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name'    => 'Date',
                                'options' => array(
                                    'format' => 'd/m/Y',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->remove('csrf');
    }
}

<?php

namespace CudiBundle\Form\Admin\Sale\Booking;

/**
 * Booking by person
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Person extends \CommonBundle\Component\Form\Admin\Form
{
    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'typeahead',
                'name'       => 'person',
                'label'      => 'Person',
                'required'   => true,
                'attributes' => array(
                    'id'    => 'person_search',
                    'style' => 'width: 400px;',
                ),
                'options' => array(
                    'input' => array(
                        'validators' => array(
                            array('name' => 'TypeaheadPerson'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'submit',
                'name'       => 'submit',
                'value'      => 'Search',
                'attributes' => array(
                    'class' => 'booking',
                    'id'    => 'search',
                ),
            )
        );
    }
}

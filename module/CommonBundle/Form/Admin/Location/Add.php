<?php

namespace CommonBundle\Form\Admin\Location;

/**
 * Add Location
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'CommonBundle\Hydrator\General\Location';

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'name',
                'label'    => 'Name',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'  => 'common_address_add',
                'name'  => 'address',
                'label' => 'Address',
            )
        );


        $this->add(
            array(
                'type'     => 'fieldset',
                'name'     => 'geographical',
                'label'    => 'Geographical',
                'elements' => array(
                    array(
                        'type'       => 'text',
                        'name'       => 'latitude',
                        'label'      => 'Latitude',
                        'required'   => true,
                        'attributes' => array(
                            'class' => 'latitude',
                        ),
                        'options'    => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                            ),
                        ),
                    ),
                    array(
                        'type'       => 'text',
                        'name'       => 'longitude',
                        'label'      => 'Longitude',
                        'required'   => true,
                        'attributes' => array(
                            'class' => 'longitude',
                        ),
                        'options'    => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'location_add', 'add');
    }
}

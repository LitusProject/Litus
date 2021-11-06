<?php

namespace ShopBundle\Form\Admin\ReservationPermission;

/**
 * Add Reservation Permission
 *
 * @author Floris Kint <floris.kint@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'ShopBundle\Hydrator\Reservation\Permission';

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'typeahead',
                'name'     => 'person',
                'label'    => 'User',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'TypeaheadPerson'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'reservations_allowed',
                'label'      => 'Reservations allowed',
                'attributes' => array(
                    'data-help' => 'Enabling this option will allow this client to reserve articles.',
                ),
            )
        );

        $this->addSubmit('Add', 'add');
    }
}

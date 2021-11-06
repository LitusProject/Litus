<?php

namespace PromBundle\Form\Admin\Bus;

/**
 * Add new buses
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'PromBundle\Hydrator\Bus';

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'name',
                'label'    => 'Bus Name',
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
                'type'     => 'datetime',
                'name'     => 'departure_time',
                'label'    => 'Departure Time',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'DateCompare',
                                'options' => array(
                                    'first_date' => 'now',
                                    'format'     => 'd/m/Y H:i',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'total_seats',
                'label'    => 'Total passengers',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Int'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'direction',
                'label'      => 'Go or Return',
                'required'   => true,
                'attributes' => array(
                    'id'      => 'direction',
                    'options' => array(
                        'Go'     => 'Go',
                        'Return' => 'Return',
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'bus_add');
    }
}

<?php

namespace LogisticsBundle\Form\Admin\Lease;

/**
 * Add a new lease
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'LogisticsBundle\Hydrator\Lease\Item';

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
                'type'     => 'text',
                'name'     => 'barcode',
                'label'    => 'Barcode',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name'    => 'Barcode',
                                'options' => array(
                                    'adapter'     => 'Ean12',
                                    'useChecksum' => false,
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'    => 'textarea',
                'name'    => 'additional_info',
                'label'   => 'Additional Info',
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'lease_add');
    }
}

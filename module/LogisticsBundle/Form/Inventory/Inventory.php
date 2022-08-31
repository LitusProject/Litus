<?php

namespace LogisticsBundle\Form\Inventory;

class Inventory extends \CommonBundle\Component\Form\Bootstrap\Form
{
    protected $hydrator = 'LogisticsBundle\Hydrator\Inventory';

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'barcode',
                'label'      => 'Barcode Product',
                'required'   => true,
                'attributes' => array(
                    'id'           => 'barcode',
                    'placeholder'  => 'Barcode',
                ),
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
                'name'     => 'name',
                'label'    => 'Product Name',
                'required' => false,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
                'attributes' => array(
                    'id'           => 'name',
                    'placeholder'  => 'Product Name',
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'amount',
                'label'    => 'Amount',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
                'attributes' => array(
                    'id'           => 'amount',
                    'placeholder'  => 'Amount',
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'expiry_date',
                'label'    => 'Expiry Date',
                'required' => false,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
                'attributes' => array(
                    'id'           => 'expiry_date',
                    'placeholder'  => 'Expiry Date',
                ),
            )
        );

        $this->addSubmit('Add/Subtract', 'inventory_add');
    }
}
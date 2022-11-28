<?php

namespace LogisticsBundle\Form\Inventory;

class Reserve extends \CommonBundle\Component\Form\Bootstrap\Form
{

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'reserve',
                'label'    => 'Reserve',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
                'attributes' => array(
                    'id'           => 'reserve',
                    'placeholder'  => 'Reserve',
                    'value' => '0',
                    'min'   => '0',
                    'max'   => 10000,
                ),
            )
        );

        $this->addSubmit('Reserve', 'inventory_reserve', 'reserve', array('id' => 'inventory_reserve'));
    }
}
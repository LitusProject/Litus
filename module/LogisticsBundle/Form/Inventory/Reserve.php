<?php

namespace LogisticsBundle\Form\Inventory;

/**
 * Edit Inventory
 *
 * @author Pedro Devogelaere <pedro.devogelaere@vtk.be>
 */

class Reserve extends \CommonBundle\Component\Form\Bootstrap\Form
{
    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'reserve',
                'label'      => 'Reserve',
                'required'   => true,
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
                'attributes' => array(
                    'id'          => 'reserve',
                    'placeholder' => 'Geef het aantal items in om te reserveren',
                ),
            )
        );

        $this->addSubmit('Reserve', 'inventory_reserve', 'reserve_button', array('id' => 'inventory_reserve'));
    }
}

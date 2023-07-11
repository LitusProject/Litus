<?php

namespace LogisticsBundle\Form\Inventory;

/**
 * Edit Inventory
 *
 * @author Pedro Devogelaere <pedro.devogelaere@vtk.be>
 */

class Edit extends \LogisticsBundle\Form\Inventory\Inventory
{
    protected $hydrator = 'LogisticsBundle\Hydrator\Inventory';

    public function init()
    {
        parent::init();

        $nameField = $this->get('name');
        $nameField->setRequired();

//        $this->remove('unit')->remove('perUnit');

        $this->remove('submit')
            ->addSubmit('Save', 'inventory_edit');
    }
}
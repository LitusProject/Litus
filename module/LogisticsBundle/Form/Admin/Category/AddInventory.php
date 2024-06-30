<?php

namespace LogisticsBundle\Form\Admin\Category;

/**
 * Form used to add a Category
 *
 * @author Pedro Devogelaere <pedro.devogelaere@vtk.be>
 */
class AddInventory extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = \LogisticsBundle\Hydrator\InventoryCategory::class;

    public function init(): void
    {
        parent::init();

        $this->add(
            array(
                'type'        => 'text',
                'name'        => 'name',
                'label'       => 'Name',
                'required'    => true,
                'attributes'    => array(
                    'placeholder'   => 'Router',
                ),
                'options' => array(
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
                'type'       => 'textarea',
                'name'       => 'description',
                'label'      => 'Description',
                'attributes'  => array(
                    'rows'    => 2,
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'add');
    }
}

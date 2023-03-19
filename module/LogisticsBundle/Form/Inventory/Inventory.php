<?php

namespace LogisticsBundle\Form\Inventory;

use LogisticsBundle\Entity\Inventory as InventoryEntity;

class Inventory extends \CommonBundle\Component\Form\Bootstrap\Form
{
    protected $hydrator = 'LogisticsBundle\Hydrator\Inventory';

    /**
     * @var InventoryEntity|null
     */
    protected $inventory;

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

//        $this->add(
//            array(
//                'type'       => 'select',
//                'name'       => 'category',
//                'label'      => 'Category',
//                'required'   => true,
//                'attributes' => array(
//                    'options' => InventoryEntity::$possibleCategories,
//                ),
//                'options'    => array(
//                    'input' => array(
//                        'filter' => array(
//                            array('name' => 'StringTrim'),
//                        ),
//                    ),
//                ),
//            )
//        );

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
                    'placeholder'  => 'Expiry date',
                    'value'        => '',
                ),
            )
        );

        $this->addSubmit('Add/Subtract', 'inventory_add');

        if ($this->inventory !== null) {
            $this->bind($this->inventory);
        }
    }

    /**
     * @param  InventoryEntity $inventory
     * @return self
     */
    public function setInventory(InventoryEntity $inventory)
    {
        $this->inventory = $inventory;

        return $this;
    }
}
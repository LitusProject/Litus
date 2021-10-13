<?php

namespace ShopBundle\Form\Admin\Product;

/**
 * Add Product
 *
 * @author Floris Kint <floris.kint@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'ShopBundle\Hydrator\Product';

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'name',
                'label'    => 'Name',
                'required' => true,
            )
        );

        $this->add(
            array(
                'type'  => 'text',
                'name'  => 'name_en',
                'label' => 'English Name',
            )
        );

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'sell_price',
                'label'    => 'Sell Price',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Price'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'available',
                'label'      => 'Available',
                'attributes' => array(
                    'data-help' => 'Enabling this option will allow clients to reserve this article.',
                    'value'     => true,
                ),
            )
        );

        $this->addSubmit('Add', 'add');
    }
}

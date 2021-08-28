<?php

namespace CudiBundle\Form\Admin\Stock;

/**
 * Stock Select options
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SelectOptions extends \CommonBundle\Component\Form\Admin\Form
{
    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'radio',
                'name'       => 'articles',
                'label'      => 'Articles',
                'required'   => true,
                'value'      => 'all',
                'attributes' => array(
                    'options' => array(
                        'all'      => 'All',
                        'internal' => 'Internal',
                        'external' => 'External',
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'radio',
                'name'       => 'order',
                'label'      => 'Order',
                'required'   => true,
                'value'      => 'barcode',
                'attributes' => array(
                    'options' => array(
                        'barcode' => 'Barcode',
                        'title'   => 'Title',
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'in_stock',
                'label' => 'Only In Stock',
            )
        );

        $this->addSubmit('Select', 'view', 'select');
    }
}

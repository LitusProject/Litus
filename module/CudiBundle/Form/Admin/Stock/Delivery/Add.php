<?php

namespace CudiBundle\Form\Admin\Stock\Delivery;

/**
 * Add Delivery
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var string
     */
    private $barcodePrefix = '';

    public function init()
    {
        parent::init();

        $this->setAttribute('id', 'deliveryForm');

        $this->add(
            array(
                'type'       => 'hidden',
                'name'       => 'add_with_virtual_order',
                'attributes' => array(
                    'id' => 'addWithVirtualOrder',
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'typeahead',
                'name'       => 'article',
                'label'      => 'Article',
                'required'   => true,
                'value'      => $this->barcodePrefix,
                'attributes' => array(
                    'id'    => 'article',
                    'style' => 'width: 400px;',
                ),
                'options'    => array(
                    'input' => array(
                        'validators' => array(
                            array('name' => 'TypeaheadSaleArticle'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'number',
                'label'      => 'Number',
                'required'   => true,
                'attributes' => array(
                    'autocomplete' => 'off',
                    'id'           => 'delivery_number',
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'Int',
                            ),
                            array(
                                'name'    => 'GreaterThan',
                                'options' => array(
                                    'min' => 0,
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'stock_add', 'add', array('id' => 'stock_add'));
    }

    /**
     * @param  string $barcodePrefix
     * @return self
     */
    public function setBarcodePrefix($barcodePrefix)
    {
        $this->barcodePrefix = $barcodePrefix;

        return $this;
    }
}

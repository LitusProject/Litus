<?php

namespace BrBundle\Form\Admin\Order;

use BrBundle\Entity\Product\Order\Entry as OrderEntry;
use LogicException;

/**
 * Edit a product.
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class EditProduct extends \BrBundle\Form\Admin\Order\AddProduct
{
    /**
     * @var OrderEntry|null The order entry to edit
     *
     * TODO: Rename to $orderEntry
     */
    private $entry;

    public function init()
    {
        parent::init();

        if ($this->entry === null) {
            throw new LogicException('Cannot edit a null order entry');
        }

        $this->remove('new_product');
        $this->remove('new_product_amount');

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'edit_product',
                'label'      => 'Product',
                'required'   => true,
                'value'      => strval($this->entry->getProduct()->getId()),
                'attributes' => array(
                    'options' => $this->createProductArray(),
                    'disable' => 'disable',
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'edit_product_amount',
                'label'      => 'Amount',
                'required'   => true,
                'attributes' => array(
                    'value' => $this->entry->getQuantity(),
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
                                'name'    => 'Between',
                                'options' => array(
                                    'min' => 1,
                                    'max' => self::MAX_ORDER_NUMBER,
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->remove('product_add')
            ->addSubmit('Save', 'product_edit');

        $this->bind($this->order);
    }

    /**
     * @param  OrderEntry $entry
     * @return self
     */
    public function setEntry(OrderEntry $entry)
    {
        $this->entry = $entry;

        return $this;
    }
}

<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Form\Admin\Order;

use BrBundle\Entity\Product\OrderEntry;
use LogicException;

/**
 * Add a product.
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class EditProduct extends AddProduct
{
    /**
     * @var entry|null The order entry to edit.
     */
    private $entry;

    public function init()
    {
        parent::init();

        if (null === $this->entry) {
            throw new LogicException('Cannot edit a null order entry');
        }

        $this->remove('new_product');
        $this->remove('new_product_amount');

        $this->add(array(
            'type'       => 'select',
            'name'       => 'edit_product',
            'label'      => 'Product',
            'required'   => true,
            'value'      => strval($this->entry->getProduct()->getId()),
            'attributes' => array(
                'options' => $this->createProductArray(),
                'disable' => 'disable',
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'edit_product_amount',
            'label'      => 'Amount',
            'required'   => true,
            'attributes' => array(
                'value' => $this->entry->getQuantity(),
            ),
            'options' => array(
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
        ));

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

<?php

namespace ShopBundle\Form\Admin\Product;

use ShopBundle\Entity\Product;

/**
 * Edit Product
 *
 * @author Floris Kint <floris.kint@litus.cc>
 */
class Edit extends \ShopBundle\Form\Admin\Product\Add
{
    /**
     * @var Product The product to edit.
     */
    private $product;

    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save', 'edit');

        $this->bind($this->product);
    }

    /**
     * @param  Product $product
     * @return self
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;

        return $this;
    }
}

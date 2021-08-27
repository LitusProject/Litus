<?php

namespace BrBundle\Form\Admin\Order;

/**
 * Add a product.
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class AddProduct extends \BrBundle\Form\Admin\Order\Add
{
    /**
     * @var array The currently used products
     */
    protected $currentProducts;

    /**
     * The maximum number allowed to enter in the corporate order form.
     */
    const MAX_ORDER_NUMBER = 12;

    public function init()
    {
        parent::init();

        foreach ($this->getElements() as $element) {
            if (in_array($element->getName(), array('csrf'))) {
                continue;
            }
            $this->remove($element->getName());
            $this->add(
                array(
                    'type'     => 'hidden',
                    'name'     => $element->getName(),
                    'value'    => $element->getValue(),
                    'required' => $element->isRequired(),
                    'options'  => $element->getOptions(),
                )
            );
        }

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'new_product',
                'label'      => 'Product',
                'required'   => true,
                'attributes' => array(
                    'options' => $this->createProductArray(),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'new_product_amount',
                'label'    => 'Amount',
                'required' => true,
                'options'  => array(
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

        $this->remove('submit')
            ->addSubmit('Add', 'product_add');
    }

    /**
     * @param  array $currentProducts
     * @return self
     */
    public function setCurrentProducts(array $currentProducts)
    {
        $this->currentProducts = $currentProducts;

        return $this;
    }

    protected function createProductArray()
    {
        $products = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Product')
            ->findAllNotOld(false);

        $productArray = array(
            '' => '',
        );
        foreach ($products as $product) {
            if (!in_array($product, $this->currentProducts)) {
                $productArray[$product->getId()] = $product->getName();
            }
        }

        return $productArray;
    }
}

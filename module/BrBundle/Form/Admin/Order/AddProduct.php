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
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Form\Admin\Order;

use BrBundle\Component\Validator\ProductName as ProductNameValidator,
    BrBundle\Entity\Company,
    BrBundle\Entity\Product\Order,
    CommonBundle\Component\Validator\Price as PriceValidator,
    CommonBundle\Entity\General\AcademicYear;

/**
 * Add a product.
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class AddProduct extends Add
{
    /**
     * @var array The currently used products
     */
    protected $_currentProducts;

    public function init()
    {
        parent::init();

        $this->remove('submit');

        foreach ($this->getElements() as $element) {
            if (in_array($element->getName(), array('csrf'))) {
                continue;
            }
            $this->remove($element->getName());
            $this->add(array(
                'type'     => 'hidden',
                'name'     => $element->getName(),
                'value'    => $element->getValue(),
                'required' => $element->isRequired(),
                'options'  => $element->getOptions(),
            ));
        }

        $this->add(array(
            'type'     => 'select',
            'name'     => 'new_product',
            'label'    => 'Product',
            'required' => true,
            'attributes' => array(
                'options' => $this->_createProductArray(),
            ),
        ));

        $this->add(array(
            'type'     => 'text',
            'name'     => 'new_product_amount',
            'label'    => 'Amount',
            'required' => true,
            'options'  => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'int',
                        ),
                    ),
                ),
            ),
        ));

        $this->addSubmit('Add', 'product_add');
    }

    /**
     * @param  array $currentProducts
     * @return self
     */
    public function setCurrentProducts(array $currentProducts)
    {
        $this->_currentProducts = $currentProducts;

        return $this;
    }

    private function _createProductArray()
    {
        $products = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Product')
            ->findByAcademicYear($this->_currentYear);

        $productArray = array(
            '' => '',
        );
        foreach ($products as $product) {
            if (!in_array($product, $this->_currentProducts) && $product->isOld() == false) {
                $productArray[$product->getId()] = $product->getName();
            }
        }

        return $productArray;
    }
}

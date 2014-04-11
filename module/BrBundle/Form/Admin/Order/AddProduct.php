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

use CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Form\Admin\Element\Textarea,
    CommonBundle\Component\Validator\Price as PriceValidator,
    CommonBundle\Entity\General\AcademicYear,
    BrBundle\Entity\Company,
    BrBundle\Entity\Product\Order,
    BrBundle\Component\Validator\ProductName as ProductNameValidator,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add a order.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class AddProduct extends \CommonBundle\Component\Form\Admin\Form
{

    /**
     * The maximum number allowed to enter in the corporate order form.
     */
    const MAX_ORDER_NUMBER = 10;

    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @var \CommonBundle\Entity\General\AcademicYear The current academic year
     */
    protected $_currentYear = null;

    /**
     * @var array Contains the input fields added for product quantities.
     */
    private $_inputs = array();

    /**
     * @var The contacts field
     */
    private $_contacts;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \CommonBundle\Entity\General\AcademicYear
     * @param mixed $opts The validator's options
     */
    public function __construct(Array $currentProducts, EntityManager $entityManager, AcademicYear $currentYear, $opts = null)
    {
        parent::__construct($opts);

        $this->_entityManager = $entityManager;

        $field = new Select('product');
        $field->setLabel('Product')
            ->setRequired()
            ->setAttribute('options', $this->_createProductArray($currentProducts));
        $this->add($field);

        $field = new Text('amount');
        $field->setLabel('Amount');
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'product_add');
        $this->add($field);
    }

    private function _createProductArray(Array $currentProducts)
    {
        $products = $this->_entityManager
            ->getRepository('BrBundle\Entity\Product')
            ->findAll();

        $productArray = array(
            '' => ''
        );
        foreach ($products as $product)
            if(! in_array($product, $currentProducts) && $product->isOld() == false)
                $productArray[$product->getId()] = $product->getName();

        return $productArray;
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        return $inputFilter;
    }
}

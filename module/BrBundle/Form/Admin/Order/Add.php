<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Form\Admin\Order;

use CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Form\Admin\Element\Textarea,
    CommonBundle\Component\Validator\Price as PriceValidator,
    CommonBundle\Entity\General\AcademicYear,
    BrBundle\Entity\Product\Order,
    BrBundle\Component\Validator\ProductName as ProductNameValidator,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add a product.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
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
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \CommonBundle\Entity\General\AcademicYear
     * @param mixed $opts The validator's options
     */
    public function __construct(EntityManager $entityManager, AcademicYear $currentYear, $opts = null)
    {
        parent::__construct($opts);

        $this->_entityManager = $entityManager;
        $this->_currentYear = $currentYear;

        $field = new Select('company');
        $field->setLabel('Company')
            ->setAttribute('options', $this->_createCompanyArray());
        $this->add($field);

        $this->addInputs();

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'product_add');
        $this->add($field);
    }

    private function _createCompanyArray()
    {
        $companies = $this->_entityManager
            ->getRepository('BrBundle\Entity\Company')
            ->findAll();

        $companyArray = array(
            '' => ''
        );
        foreach ($companies as $company)
            $companyArray[$company->getId()] = $company->getName();

        return $companyArray;
    }

    private function addInputs()
    {

        $products = $this->_entityManager
            ->getRepository('BrBundle\Entity\Product')
            ->findByAcademicYear($this->_currentYear);

        foreach ($products as $product) {

            $field = new Text('product-' . $product->getId());
            $field->setLabel($product->getName())
                ->setAttribute('class', 'input-very-mini')
                ->setAttribute('placeholder', '0');
            $this->add($field);

            $this->_inputs[] = $field;
        }
    }

    public function populateFromOrder(Order $order)
    {
        $formData = array(
            'company' => $order->getCompany()->getId(),
        );

        $products = $this->_entityManager
            ->getRepository('BrBundle\Entity\Product')
            ->findByAcademicYear($this->_currentYear);

        foreach ($products as $product) {
            $orderEntry = $this->_entityManager->getRepository('BrBundle\Entity\Product\OrderEntry')
                ->findOneByOrderAndProduct($order, $product);

            $formData['product-' . $product->getId()] = null === $orderEntry ? 0 : $orderEntry->getQuantity();
        }

        $this->setData($formData);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        foreach ($this->_inputs as $input) {
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => $input->getName(),
                        'required' => false,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'digits',
                            ),
                            array(
                                'name' => 'between',
                                'options' => array(
                                    'min' => 0,
                                    'max' => self::MAX_ORDER_NUMBER,
                                ),
                            ),
                        ),
                    )
                )
            );
        }

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'company',
                    'required' => true,
                )
            )
        );

        return $inputFilter;
    }
}

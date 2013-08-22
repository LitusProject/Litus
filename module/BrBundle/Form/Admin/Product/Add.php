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

namespace BrBundle\Form\Admin\Product;

use CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Form\Admin\Element\Textarea,
    CommonBundle\Component\Validator\Price as PriceValidator,
    BrBundle\Entity\Product,
    BrBundle\Component\Validator\ProductName as ProductNameValidator,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add a product.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param mixed $opts The validator's options
     */
    public function __construct(EntityManager $entityManager, $opts = null)
    {
        parent::__construct($opts);

        $this->_entityManager = $entityManager;

        $field = new Text('name');
        $field->setLabel('Name')
            ->setRequired();
        $this->add($field);

        $field = new Text('price');
        $field->setLabel('Price')
            ->setRequired()
            ->setValue('0');
        $this->add($field);

        $field = new Select('vat_type');
        $field->setLabel('VAT Type')
            ->setRequired()
            ->setAttribute('options', $this->_getVatTypes($entityManager));
        $this->add($field);

        $field = new Text('invoice_description');
        $field->setLabel('Description on Invoice')
            ->setRequired(false);
        $this->add($field);

        $field = new Textarea('contract_text');
        $field->setLabel('Content')
            ->setRequired()
            ->setValue('<entry></entry>');
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'product_add');
        $this->add($field);
    }

    public function populateFromProduct(Product $product)
    {
        $formData = array(
            'name'  => $product->getName(),
            'price' => number_format($product->getPrice()/100, 2),
            'vat_type' => $product->getVatType(),
            'invoice_description' => $product->getInvoiceDescription(),
            'contract_text' => $product->getContractText(),
        );

        $this->setData($formData);
    }


    /**
     * Retrieve the different VAT types applicable.
     *
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     */
    private function _getVatTypes(EntityManager $entityManager)
    {
        $types =  $entityManager->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.vat_types');
        $types = unserialize($types);
        $typesArray = array();
        foreach ($types as $type => $value)
            $typesArray[$type] = $value . '%';

        return $typesArray;
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'name',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new ProductNameValidator($this->_entityManager),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'price',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new PriceValidator(),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'invoice_description',
                    'required' => false,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'content',
                    'required' => false,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}

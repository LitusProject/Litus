<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
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

namespace CudiBundle\Form\Admin\Sales\Article;

use CommonBundle\Component\Form\Admin\Element\Checkbox,
    CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Validator\Price as PriceValidator,
    CudiBundle\Component\Validator\Sales\Article\Barcodes\Unique as UniqueBarcodeValidator,
    CudiBundle\Entity\Sales\Article,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add Article
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;

        $field = new Text('purchase_price');
        $field->setLabel('Purchase Price')
            ->setRequired();
        $this->add($field);

        $field = new Text('sell_price');
        $field->setLabel('Sell Price')
            ->setRequired();
        $this->add($field);

        $field = new Text('barcode');
        $field->setLabel('Barcode')
            ->setAttribute('class', 'disableEnter')
            ->setRequired();
        $this->add($field);

        $field = new Select('organization');
        $field->setLabel('Organization')
            ->setRequired()
            ->setAttribute('options', $this->_getOrganizations());
        $this->add($field);

        $field = new Select('supplier');
        $field->setLabel('Supplier')
            ->setRequired()
            ->setAttribute('options', $this->_getSuppliers());
        $this->add($field);

        $field = new Checkbox('bookable');
        $field->setLabel('Bookable');
        $this->add($field);

        $field = new Checkbox('unbookable');
        $field->setLabel('Unbookable');
        $this->add($field);

        $field = new Checkbox('sellable');
        $field->setLabel('Sellable')
            ->setValue(true);
        $this->add($field);

        $field = new Checkbox('can_expire');
        $field->setLabel('Can Expire');
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'article_add');
        $this->add($field);
    }

    private function _getSuppliers()
    {
        $suppliers = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Supplier')
            ->findAll();
        $supplierOptions = array();
        foreach($suppliers as $item)
            $supplierOptions[$item->getId()] = $item->getName();

        return $supplierOptions;
    }

    private function _getOrganizations()
    {
        $organizations = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();
        $organizationsOptions = array();
        foreach($organizations as $organization)
            $organizationsOptions[$organization->getId()] = $organization->getName();

        return $organizationsOptions;
    }

    public function populateFromArticle(Article $article)
    {
        $this->setData(
            array(
                'purchase_price' => number_format($article->getPurchasePrice()/100, 2),
                'sell_price' => number_format($article->getSellPrice()/100, 2),
                'barcode' => $article->getBarcode(),
                'supplier' => $article->getSupplier()->getId(),
                'bookable' => $article->isBookable(),
                'unbookable' => $article->isUnbookable(),
                'sellable' => $article->isSellable(),
                'can_expire' => $article->canExpire()
            )
        );
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'purchase_price',
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
                    'name'     => 'sell_price',
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
                    'name'     => 'barcode',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'barcode',
                            'options' => array(
                                'adapter'     => 'Ean12',
                                'useChecksum' => false,
                            ),
                        ),
                        new UniqueBarcodeValidator($this->_entityManager),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'organization',
                    'required' => true,
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'supplier',
                    'required' => true,
                )
            )
        );

        return $inputFilter;
    }
}

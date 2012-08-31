<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Form\Admin\Sales\Article;

use CommonBundle\Component\Form\Admin\Element\Checkbox,
    CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Validator\Price as PriceValidator,
    CommonBundle\Entity\General\AcademicYear as AcademicYear,
    CudiBundle\Component\Validator\UniqueArticleBarcode as UniqueArticleBarcodeValidator,
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
     * @var \CommonBundle\Entity\General\AcademicYear
     */
    protected $_academicYear;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, AcademicYear $academicYear, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;
        $this->_academicYear = $academicYear;

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
                'can_expire' => $article->canExpire()
            )
        );
    }

    public function getInputFilter()
    {
        if ($this->_inputFilter == null) {
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
                            new UniqueArticleBarcodeValidator($this->_entityManager, $this->_academicYear),
                        ),
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

            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
}

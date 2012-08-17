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

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
    CommonBundle\Component\Validator\Price as PriceValidator,
    CommonBundle\Entity\General\AcademicYear as AcademicYear,
    CudiBundle\Component\Validator\UniqueArticleBarcode as UniqueArticleBarcodeValidator,
    CudiBundle\Entity\Sales\Article,
    Doctrine\ORM\EntityManager,
    Zend\Form\Element\Checkbox,
    Zend\Form\Element\Select,
    Zend\Form\Element\Submit,
    Zend\Form\Element\Text,
    Zend\Validator\Barcode as BarcodeValidator;

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

    public function __construct(EntityManager $entityManager, AcademicYear $academicYear, $opts = null)
    {
        parent::__construct($opts);

        $this->_entityManager = $entityManager;

        $field = new Text('purchase_price');
        $field->setLabel('Purchase Price')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()))
            ->addValidator(new PriceValidator());
        $this->addElement($field);

        $field = new Text('sell_price');
        $field->setLabel('Sell Price')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()))
            ->addValidator(new PriceValidator());
        $this->addElement($field);

        $field = new Text('barcode');
        $field->setLabel('Barcode')
            ->setAttrib('class', 'disableEnter')
            ->setRequired()
            ->addValidator(
                new BarcodeValidator(
                    array(
                        'adapter'     => 'Ean12',
                        'useChecksum' => false,
                    )
                )
            )
            ->addValidator(new UniqueArticleBarcodeValidator($this->_entityManager, $academicYear))
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Select('supplier');
        $field->setLabel('Supplier')
            ->setRequired()
            ->setMultiOptions($this->_getSuppliers())
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Checkbox('bookable');
        $field->setLabel('Bookable')
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Checkbox('unbookable');
        $field->setLabel('Unbookable')
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Checkbox('can_expire');
        $field->setLabel('Can Expire')
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Submit('submit');
        $field->setLabel('Add')
                ->setAttrib('class', 'article_add')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
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
        $this->populate(array(
            'purchase_price' => number_format($article->getPurchasePrice()/100, 2),
            'sell_price' => number_format($article->getSellPrice()/100, 2),
            'barcode' => $article->getBarcode(),
            'supplier' => $article->getSupplier()->getId(),
            'bookable' => $article->isBookable(),
            'unbookable' => $article->isUnbookable(),
            'can_expire' => $article->canExpire()
        ));
    }
}

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

namespace CudiBundle\Entity\Sales;

use CommonBundle\Entity\General\AcademicYear,
    CudiBundle\Entity\Article as MainArticle,
    CudiBundle\Entity\Sales\Articles\Barcode,
    CudiBundle\Entity\Supplier as Supplier,
    DateTime,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sales\Article")
 * @ORM\Table(name="cudi.sales_articles")
 */
class Article
{
    /**
     * @var integer The ID of this article
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \DateTime The time the article was created
     *
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

    /**
     * @var \CudiBundle\Entity\Article The main article of this sale article
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Article")
     * @ORM\JoinColumn(name="article", referencedColumnName="id")
     */
    private $mainArticle;

    /**
     * @var integer The barcode of the article
     *
     * @ORM\Column(type="bigint")
     */
    private $barcode;

    /**
     * @var integer The purchase price of the article
     *
     * @ORM\Column(name="purchase_price", type="bigint")
     */
    private $purchasePrice;

    /**
     * @var integer The sell price of the article
     *
     * @ORM\Column(name="sell_price", type="bigint")
     */
    private $sellPrice;

    /**
     * @var boolean Flag whether the article is bookable
     *
     * @ORM\Column(type="boolean")
     */
    private $bookable;

    /**
     * @var boolean Flag whether the article is unbookable
     *
     * @ORM\Column(type="boolean")
     */
    private $unbookable;

    /**
     * @var \CudiBundle\Entity\Supplier The supplier of the article
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Supplier")
     * @ORM\JoinColumn(name="supplier", referencedColumnName="id")
     */
    private $supplier;

    /**
     * @var boolean Flag whether the article can expire
     *
     * @ORM\Column(type="boolean")
     */
    private $canExpire;

    /**
     * @var \CommonBundle\Entity\General\AcademicYear The year of the article
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @ORM\JoinColumn(name="academic_year", referencedColumnName="id")
     */
    private $academicYear;

    /**
     * @var integer The version number of this article
     *
     * @ORM\Column(name="version_number", type="smallint", nullable=true)
     */
    private $versionNumber;

    /**
     * @var integer The current number in stock
     *
     * @ORM\Column(name="stock_value", type="bigint")
     */
    private $stockValue;

    /**
     * @var boolean The flag whether the article is old or not
     *
     * @ORM\Column(name="is_history", type="boolean")
     */
    private $isHistory;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection The discounts of the article
     *
     * @ORM\OneToMany(targetEntity="CudiBundle\Entity\Sales\Discounts\Discount", mappedBy="article")
     */
    private $discounts;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection The additional barcodes of the article
     *
     * @ORM\OneToMany(targetEntity="CudiBundle\Entity\Sales\Articles\Barcode", mappedBy="article", cascade={"persist", "remove"})
     */
    private $additionalBarcodes;

    /**
     * @param \CudiBundle\Entity\Article $mainArticle The main article of this sale article
     * @param integer $barcode  The barcode of the article
     * @param integer $purchasePrice The purchase price of the articl
     * @param integer $sellPrice The sell price of the article
     * @param boolean $bookable Flag whether the article is bookable
     * @param boolean $unbookable Flag whether the article is unbookable
     * @param \CudiBundle\Entity\Supplier $supplier The supplier of the article
     * @param boolean $canExpire Flag whether the aritcle can expire
     */
    public function __construct(MainArticle $mainArticle, $barcode, $purchasePrice, $sellPrice, $bookable, $unbookable, Supplier $supplier, $canExpire, AcademicYear $academicYear)
    {
        $this->setMainArticle($mainArticle)
            ->setBarcode($barcode)
            ->setPurchasePrice($purchasePrice)
            ->setSellPrice($sellPrice)
            ->setIsBookable($bookable)
            ->setIsUnbookable($unbookable)
            ->setSupplier($supplier)
            ->setCanExpire($canExpire)
            ->setVersionNumber(1)
            ->setIsHistory(false)
            ->setAcademicYear($academicYear);
        $this->discounts = new ArrayCollection();
        $this->additionalBarcodes = new ArrayCollection();
        $this->timestamp = new DateTime();
        $this->stockValue = 0;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return \CudiBundle\Entity\Article
     */
    public function getMainArticle()
    {
        return $this->mainArticle;
    }

    /**
     * @param \CudiBundle\Entity\Article $mainArticle
     *
     * @return \CudiBundle\Entity\Sales\Article
     */
    public function setMainArticle($mainArticle)
    {
        $this->mainArticle = $mainArticle;
        return $this;
    }

    /**
     * @return integer
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * @param integer $barcode
     *
     * @return \CudiBundle\Entity\Article
     */
    public function setBarcode($barcode)
    {
        $this->barcode = $barcode;
        return $this;
    }

    /**
     * @return integer
     */
    public function getPurchasePrice()
    {
        return $this->purchasePrice;
    }

    /**
     * @param float $purchasePrice
     *
     * @return \CudiBundle\Entity\Sales\Article
     */
    public function setPurchasePrice($purchasePrice)
    {
        $this->purchasePrice = $purchasePrice * 100;
        return $this;
    }

    /**
     * @return integer
     */
    public function getSellPrice()
    {
        return $this->sellPrice;
    }

    /**
     * @param float $sellPrice
     *
     * @return \CudiBundle\Entity\Sales\Article
     */
    public function setSellPrice($sellPrice)
    {
        $this->sellPrice = $sellPrice * 100;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isBookable()
    {
        return $this->bookable;
    }

    /**
     * @param boolean $bookable
     *
     * @return \CudiBundle\Entity\Sales\Article
     */
    public function setIsBookable($bookable)
    {
        $this->bookable = $bookable;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isUnbookable()
    {
        return $this->unbookable;
    }

    /**
     * @param boolean $unbookable
     *
     * @return \CudiBundle\Entity\Sales\Article
     */
    public function setIsUnbookable($unbookable)
    {
        $this->unbookable = $unbookable;
        return $this;
    }

    /**
     * @return \CudiBundle\Entity\Suppplier
     */
    public function getSupplier()
    {
        return $this->supplier;
    }

    /**
     * @param \CudiBundle\Entity\Supplier $supplier
     *
     * @return \CudiBundle\Entity\Sales\Article
     */
    public function setSupplier(Supplier $supplier)
    {
        $this->supplier = $supplier;
        return $this;
    }

    /**
     * @return boolean
     */
    public function canExpire()
    {
        return $this->canExpire;
    }

    /**
     * @param boolean $canExpire
     *
     * @return \CudiBundle\Entity\Sales\Article
     */
    public function setCanExpire($canExpire)
    {
        $this->canExpire = $canExpire;
        return $this;
    }

    /**
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    public function getAcademicYear()
    {
        return $this->academicYear;
    }

    /**
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear
     *
     * @return \CudiBundle\Entity\Sales\Article
     */
    public function setAcademicYear(AcademicYear $academicYear)
    {
        $this->academicYear = $academicYear;
        return $this;
    }

    /**
     * @return integer
     */
    public function getVersionNumber()
    {
        return $this->versionNumber;
    }

    /**
     * @param integer $versionNumber
     *
     * @return \CudiBundle\Entity\Sales\Article
     */
    public function setVersionNumber($versionNumber)
    {
        $this->versionNumber = $versionNumber;
        return $this;
    }

    /**
     * @return integer
     */
    public function getStockValue()
    {
        return $this->stockValue;
    }

    /**
     * @param integer $stockValue
     *
     * @return \CudiBundle\Entity\Sales\Article
     */
    public function setStockValue($stockValue)
    {
        $this->stockValue = $stockValue < 0 ? 0 : $stockValue;
        return $this;
    }

    /**
     * @param integer $stockValue
     *
     * @return \CudiBundle\Entity\Sales\Article
     */
    public function addStockValue($stockValue)
    {
        $this->setStockValue($this->stockValue + $stockValue);
        return $this;
    }

    /**
     * @return boolean
     */
    public function isHistory()
    {
        return $this->isHistory;
    }

    /**
     * @param boolean $isHistory
     *
     * @return \CudiBundle\Entity\Sales\Article
     */
    public function setIsHistory($isHistory)
    {
        $this->isHistory = $isHistory;
        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getDiscounts()
    {
        return $this->discounts;
    }

    /**
     * @param \CudiBundle\Entity\Sales\Articles\Barcode $barcode
     *
     * @return \CudiBundle\Entity\Sales\Article
     */
    public function addAdditionalBarcode(Barcode $barcode)
    {
        $this->additionalBarcodes->add($barcode);
        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getAdditionalBarcodes()
    {
        return $this->additionalBarcodes;
    }

    /**
     * @return \CudiBundle\Entity\Sales\Article
     */
    public function duplicate()
    {
        $article = new Article(
            $this->getMainArticle(),
            $this->getBarcode(),
            $this->getPurchasePrice()/100,
            $this->getSellPrice()/100,
            $this->isBookable(),
            $this->isUnbookable(),
            $this->getSupplier(),
            $this->canExpire(),
            $this->getAcademicYear()
        );
        foreach($this->additionalBarcodes as $barcode)
            $article->addAdditionalBarcode(new Barcode($article, $barcode->getBarcode()));

        return $article;
    }
}

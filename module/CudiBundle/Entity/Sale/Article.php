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

namespace CudiBundle\Entity\Sale;

use CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\General\Organization,
    CommonBundle\Entity\User\Person,
    CudiBundle\Entity\Article as MainArticle,
    CudiBundle\Entity\Sale\Article\Barcode,
    CudiBundle\Entity\Sale\Article\Restriction\Member as MemberRestriction,
    CudiBundle\Entity\Supplier,
    DateTime,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sale\Article")
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
     * @var DateTime The time the article was created
     *
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

    /**
     * @var MainArticle The main article of this sale article
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Article")
     * @ORM\JoinColumn(name="article", referencedColumnName="id")
     */
    private $mainArticle;

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
     * @var boolean Flag whether the article is sellable
     *
     * @ORM\Column(type="boolean")
     */
    private $sellable;

    /**
     * @var Supplier The supplier of the article
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
     * @var ArrayCollection The discounts of the article
     *
     * @ORM\OneToMany(targetEntity="CudiBundle\Entity\Sale\Article\Discount\Discount", mappedBy="article")
     */
    private $discounts;

    /**
     * @var ArrayCollection The barcodes of the article
     *
     * @ORM\OneToMany(targetEntity="CudiBundle\Entity\Sale\Article\Barcode", mappedBy="article", cascade={"persist", "remove"})
     */
    private $barcodes;

    /**
     * @var ArrayCollection The restrictions of the article
     *
     * @ORM\OneToMany(targetEntity="CudiBundle\Entity\Sale\Article\Restriction", mappedBy="article", cascade={"persist", "remove"})
     */
    private $restrictions;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param MainArticle $mainArticle The main article of this sale article
     */
    public function __construct(MainArticle $mainArticle)
    {
        $this->discounts = new ArrayCollection();
        $this->barcodes = new ArrayCollection();

        $this->setMainArticle($mainArticle);
        $this->timestamp = new DateTime();
        $this->stockValue = 0;
        $this->isHistory = false;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param DateTime $timestamp
     *
     * @return self
     */
    public function setTimestamp(DateTime $timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * @return MainArticle
     */
    public function getMainArticle()
    {
        return $this->mainArticle;
    }

    /**
     * @param MainArticle $mainArticle
     *
     * @return self
     */
    public function setMainArticle(MainArticle $mainArticle)
    {
        $this->mainArticle = $mainArticle;

        return $this;
    }

    /**
     * @return integer
     */
    public function getBarcode()
    {
        foreach ($this->barcodes as $barcode) {
            if ($barcode->isMain()) {
                return $barcode->getBarcode();
            }
        }
    }

    /**
     * Removes all barcodes
     *
     * @return self
     */
    public function clearBarcode()
    {
        $this->barcodes->clear();

        return $this;
    }

    /**
     * @param integer $barcode
     *
     * @return self
     */
    public function setBarcode($barcode)
    {
        if ('' == $barcode) {
            return $this;
        }

        $main = null;
        $found = null;
        foreach ($this->barcodes as $object) {
            if ($object->isMain()) {
                $main = $object;
            }
            if ($object->getBarcode() == $barcode) {
                $found = $object;
            }
        }

        if (!(null !== $main && $main->getBarcode() == $barcode)) {
            if ($main) {
                $main->setIsMain(false);
            }

            if ($found) {
                $found->setIsMain(true);
            } else {
                $this->addBarcode(new Barcode($this, $barcode, true));
            }
        }

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
     * @return self
     */
    public function setPurchasePrice($purchasePrice)
    {
        $this->purchasePrice = (int) round(str_replace(',', '.', $purchasePrice) * 100);

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
     * @return self
     */
    public function setSellPrice($sellPrice)
    {
        $this->sellPrice = (int) round(str_replace(',', '.', $sellPrice) * 100);

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
     * @return self
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
     * @return self
     */
    public function setIsUnbookable($unbookable)
    {
        $this->unbookable = $unbookable;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isSellable()
    {
        return $this->sellable;
    }

    /**
     * @param boolean $sellable
     *
     * @return self
     */
    public function setIsSellable($sellable)
    {
        $this->sellable = $sellable;

        return $this;
    }

    /**
     * @return Supplier
     */
    public function getSupplier()
    {
        return $this->supplier;
    }

    /**
     * @param Supplier $supplier
     *
     * @return self
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
     * @return self
     */
    public function setCanExpire($canExpire)
    {
        $this->canExpire = $canExpire;

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
     * @return self
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
     * @return self
     */
    public function setStockValue($stockValue)
    {
        $this->stockValue = $stockValue < 0 ? 0 : $stockValue;

        return $this;
    }

    /**
     * @param integer $stockValue
     *
     * @return self
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
     * @return self
     */
    public function setIsHistory($isHistory)
    {
        $this->isHistory = $isHistory;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getDiscounts()
    {
        return $this->discounts;
    }

    /**
     * @param Barcode $barcode
     *
     * @return self
     */
    public function addBarcode(Barcode $barcode)
    {
        $this->barcodes->add($barcode);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getBarcodes()
    {
        return $this->barcodes;
    }

    /**
     * @return ArrayCollection
     */
    public function getRestrictions()
    {
        return $this->restrictions;
    }

    /**
     * @param Person        $person
     * @param EntityManager $entityManager
     *
     * @return boolean
     */
    public function canBook(Person $person, EntityManager $entityManager)
    {
        $restrictions = $this->restrictions->toArray();

        $onlyMember = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.booking_only_member');

        $memberRestriction = $entityManager->getRepository('CudiBundle\Entity\Sale\Article\Restriction\Member')
            ->findOneByArticle($this);

        if ($onlyMember && null === $memberRestriction) {
            $restrictions[] = new MemberRestriction($this, false);
        }

        foreach ($restrictions as $restriction) {
            if (!$restriction->canBook($person, $entityManager)) {
                return false;
            }
        }

        return true;
    }

    public function __clone()
    {
        $this->timestamp = new DateTime();
        $this->stockValue = 0;
        $this->id = null;

        $this->barcodes = new ArrayCollection();
        $barcodes = $this->barcodes;

        foreach ($barcodes as $barcode) {
            $this->addBarcode(new Barcode($this, $barcode->getBarcode()));
        }
    }

    /**
     * @param EntityManager $entityManager
     *
     * @return Article
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        return $this;
    }

    /**
     * @param  AcademicYear $academicYear
     * @param  Organization $organization
     * @return integer
     */
    public function getNumberSold(AcademicYear $academicYear, Organization $organization = null)
    {
        return $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\SaleItem')
            ->findNumberByArticleAndAcademicYear($this, $academicYear, $organization);
    }

    /**
     * @param  AcademicYear $academicYear
     * @param  Organization $organization
     * @return integer
     */
    public function getNumberSoldToMembers(AcademicYear $academicYear, Organization $organization = null)
    {
        return $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\SaleItem')
            ->findNumberByArticleAndAcademicYearAndMember($this, $academicYear, $organization);
    }

    /**
     * @param  AcademicYear $academicYear
     * @param  string       $discount
     * @param  Organization $organization
     * @return integer
     */
    public function getNumberSoldWithDiscount(AcademicYear $academicYear, $discount, Organization $organization = null)
    {
        return $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\SaleItem')
            ->findNumberByArticleAndAcademicYearAndDiscount($this, $academicYear, $discount, $organization);
    }

    /**
     * @param  AcademicYear $academicYear
     * @param  Organization $organization
     * @return integer
     */
    public function getNumberReturned(AcademicYear $academicYear, Organization $organization = null)
    {
        return $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\ReturnItem')
            ->findNumberByArticleAndAcademicYear($this, $academicYear, $organization);
    }

    /**
     * @param  AcademicYear $academicYear
     * @return integer
     */
    public function getNumberDelivered(AcademicYear $academicYear)
    {
        return $this->entityManager
            ->getRepository('CudiBundle\Entity\Stock\Delivery')
            ->findNumberByArticleAndAcademicYear($this, $academicYear);
    }

    /**
     * @param  AcademicYear $academicYear
     * @param  Organization $organization
     * @return integer
     */
    public function getTotalRevenue(AcademicYear $academicYear, Organization $organization = null)
    {
        return $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\SaleItem')
            ->findTotalRevenueByArticleAndAcademicYear($this, $academicYear, $organization);
    }
}

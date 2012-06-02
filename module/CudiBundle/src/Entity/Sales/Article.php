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
 
namespace CudiBundle\Entity\Sales;

use CudiBundle\Entity\Article as MainArticle,
    CudiBundle\Entity\Supplier as Supplier,
    DateTime;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Sales\Article")
 * @Table(name="cudi.sales_articles")
 */
class Article
{
    /**
     * @var integer The ID of this article
     *
     * @Id
     * @GeneratedValue
     * @Column(type="bigint")
     */
    private $id;
    
    /**
     * @var \DateTime The time the article was created
     *
     * @Column(type="datetime")
     */
    private $timestamp;

    /**
     * @var \CudiBundle\Entity\Article The main article of this sale article
     *
     * @ManyToOne(targetEntity="CudiBundle\Entity\Article")
     * @JoinColumn(name="article", referencedColumnName="id")
     */
    private $mainArticle;
    
    /**
     * @var integer The barcode of the article
     *
     * @Column(type="bigint")
     */
    private $barcode;
    
    /**
     * @var integer The purchase price of the article
     *
     * @Column(name="purchase_price", type="bigint")
     */
    private $purchasePrice;
    
    /**
     * @var integer The sell price of the article
     *
     * @Column(name="sell_price", type="bigint")
     */
    private $sellPrice;
    
    /**
     * @var boolean Flag whether the article is bookable
     *
     * @Column(type="boolean")
     */
    private $bookable;

    /**
     * @var boolean Flag whether the article is unbookable
     *
     * @Column(type="boolean")
     */
    private $unbookable;

    /**
     * @var \CudiBundle\Entity\Supplier The supplier of the article
     *
     * @ManyToOne(targetEntity="CudiBundle\Entity\Supplier")
     * @JoinColumn(name="supplier", referencedColumnName="id")
     */
    private $supplier;

	/**
	 * @var boolean Flag whether the aritcle can expire
	 *
	 * @Column(type="boolean")
	 */
	private $canExpire;
    
    /**
     * @var integer The version number of this article
     * 
     * @Column(name="version_number", type="smallint", nullable=true)
     */
    private $versionNumber;
    
    /**
     * @var integer The current number in stock
     *
     * @Column(name="stock_value", type="bigint")
     */
    private $stockValue;
    
    /**
     * @var boolean The flag whether the article is old or not
     *
     * @Column(name="is_history", type="boolean")
     */
    private $isHistory;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection The discounts of the article
     *
     * @OneToMany(targetEntity="CudiBundle\Entity\Sales\Discounts\Discount", mappedBy="article")
     */
    private $discounts;
    
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
    public function __construct(MainArticle $mainArticle, $barcode, $purchasePrice, $sellPrice, $bookable, $unbookable, Supplier $supplier, $canExpire)
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
            ->setIsHistory(false);
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
	    $this->stockValue = $stockValue;
	    return $this;
	}
	
	/**
	 * @param integer $stockValue
	 *
	 * @return \CudiBundle\Entity\Sales\Article
	 */
	public function addStockValue($stockValue)
	{
	    $this->stockValue += $stockValue;
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
	 * @return \CudiBundle\Entity\Article
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
}
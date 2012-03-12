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
 
namespace CudiBundle\Entity\Articles;

use CudiBundle\Entity\Articles\MetaInfo,
	CudiBundle\Entity\Stock\StockItem,
	CudiBundle\Entity\Supplier,
	Doctrine\ORM\EntityManager;

/**
 * @MappedSuperclass
 */
abstract class Stock extends \CudiBundle\Entity\Article
{
    /**
     * @Column(name="purchase_price", type="bigint")
     */
    private $purchasePrice;

    /**
     * @Column(name="sell_price", type="bigint")
     */
    private $sellPrice;

    /**
     * @Column(name="sell_price_members", type="bigint")
     */
    private $sellPriceMembers;

    /**
     * @Column(type="bigint")
     */
    private $barcode;

    /**
     * @Column(type="boolean")
     */
    private $bookable;

    /**
     * @Column(type="boolean")
     */
    private $unbookable;

    /**
     * @ManyToOne(targetEntity="CudiBundle\Entity\Supplier")
     * @JoinColumn(name="supplier", referencedColumnName="id")
     */
    private $supplier;

	/**
	 * @Column(type="boolean")
	 */
	private $canExpire;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param string $title The title of the article
     * @param \CudiBundle\Entity\Articles\MetaInfo $metaInfo An unlinked metainfo object to link to this article.
     * @param float $purchasePrice The purchase price of this article.
     * @param float $sellPrice The sell price of this article for non-members.
     * @param float $sellPriceMembers The sell price of this article for members.
     * @param integer $barcode This article's barcode.
     * @param boolean $bookable Indicates whether the article can be booked.
     * @param boolean $unbookable Indicates whether the article can be unbooked.
     * @param \CudiBundle\Entity\Supplier $supplier The supplier of the stock item.
     * @param boolean $canExpire Whether the article can expire.
     */
    public function __construct(EntityManager $entityManager, $title, MetaInfo $metaInfo, $purchasePrice, $sellPrice, $sellPriceMembers, $barcode, $bookable, $unbookable, Supplier $supplier, $canExpire)
    {
        parent::__construct($title, $metaInfo);

        $this->setPurchasePrice($purchasePrice)
			->setSellPrice($sellPrice)
			->setSellPriceMembers($sellPriceMembers)
			->setBarcode($barcode)
			->setSupplier($supplier)
			->setIsBookable($bookable)
			->setIsUnbookable($unbookable)
			->setCanExpire($canExpire);
			
		$stockItem = new StockItem($this);
		$entityManager->persist($stockItem);
    }

	/**
	 * @return float
	 */
	public function getPurchasePrice()
	{
		return $this->purchasePrice;
	}
	
	/**
     * @param float $purchasePrice
	 *
     * @return CudiBundle\Entity\Articles\Stock
     */
	public function setPurchasePrice($purchasePrice)
	{
		$this->purchasePrice = $purchasePrice*100;
		return $this;
	}
	
	/**
	 * @return float
	 */
	public function getSellPrice()
	{
		return $this->sellPrice;
	}
	
	/**
     * @param float $sellPrice
	 *
     * @return CudiBundle\Entity\Articles\Stock
     */
	public function setSellPrice($sellPrice)
	{
		$this->sellPrice = $sellPrice*100;
		return $this;
	}
	
	/**
	 * @return float
	 */
	public function getSellPriceMembers()
	{
		return $this->sellPriceMembers;
	}
	
	/**
     * @param float $sellPriceMembers
	 *
     * @return CudiBundle\Entity\Articles\Stock
     */
	public function setSellPriceMembers($sellPriceMembers)
	{
		$this->sellPriceMembers = $sellPriceMembers*100;
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
     * @return CudiBundle\Entity\Articles\Stock
     */
	public function setBarcode($barcode)
	{
	    if (strlen($barcode) == 13)
	        $barcode = floor($barcode / 10);
	        
		$this->barcode = $barcode;
		return $this;
	}
	
	/**
	 * @return CudiBundle\Entity\Suppplier
	 */
	public function getSupplier()
	{
		return $this->supplier;
	}
	
	/**
     * @param CudiBundle\Entity\Supplier $supplier
	 *
     * @return CudiBundle\Entity\Articles\Stock
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
     * @return CudiBundle\Entity\Articles\Stock
     */
	public function setCanExpire($canExpire)
	{
		$this->canExpire = $canExpire;
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
     * @return CudiBundle\Entity\Articles\Stock
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
     * @return CudiBundle\Entity\Articles\Stock
     */
	public function setIsUnbookable($unbookable)
	{
		$this->unbookable = $unbookable;
		return $this;
	}
	
	/**
	 * @return boolean
	 */
	public function isStock()
	{
		return true;
	}
}
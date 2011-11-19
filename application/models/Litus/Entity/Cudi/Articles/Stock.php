<?php

namespace Litus\Entity\Cudi\Articles;

use \Litus\Entity\Cudi\Stock\StockItem;

use \Litus\Application\Resource\Doctrine as DoctrineResource;

use \Zend\Registry;

/**
 * @MappedSuperclass
 */
abstract class Stock extends \Litus\Entity\Cudi\Article
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
     * @Column(type="integer")
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
     * @ManyToOne(targetEntity="Litus\Entity\Cudi\Supplier")
     * @JoinColumn(name="supplier", referencedColumnName="id")
     */
    private $supplier;

	/**
	 * @Column(type="boolean")
	 */
	private $canExpire;

    /**
     * @param string $title The title of the article
     * @param Litus\Entity\Cudi\Articles\MetaInfo $metaInfo An unlinked metainfo object to link to this article.
     * @param bigint $purchase_price The purchase price of this article.
     * @param bigint $sellPrice The sell price of this article for non-members.
     * @param bigint $sellPriceMembers The sell price of this article for members.
     * @param smallint $barcode This article's barcode.
     * @param boolean $bookable Indicates whether the article can be booked.
     * @param boolean $unbookable Indicates whether the article can be unbooked.
     * @param Litus\Entity\Cudi\Supplier $supplier The supplier of the stock item.
     */
    public function __construct($title, $metaInfo, $purchase_price, $sellPrice, $sellPriceMembers, $barcode, $bookable, $unbookable, $supplier, $canExpire)
    {
        parent::__construct($title, $metaInfo);

        $this->setPurchasePrice($purchase_price)
			->setSellPrice($sellPrice)
			->setSellPriceMembers($sellPriceMembers)
			->setBarcode($barcode)
			->setSupplier($supplier)
			->setIsBookable($bookable)
			->setIsUnbookable($unbookable)
			->setCanExpire($canExpire);
			
		$stockItem = new StockItem($this);
		Registry::get(DoctrineResource::REGISTRY_KEY)->persist($stockItem);
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
     * @return \Litus\Entity\Cudi\Articles\Stock
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
     * @return \Litus\Entity\Cudi\Articles\Stock
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
     * @return \Litus\Entity\Cudi\Articles\Stock
     */
	public function setSellPriceMembers($sellPriceMembers)
	{
		$this->sellPriceMembers = $sellPriceMembers*100;
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getBarcode()
	{
		return $this->barcode;
	}
	
	/**
     * @param int $barcode
	 *
     * @return \Litus\Entity\Cudi\Articles\Stock
     */
	public function setBarcode($barcode)
	{
		$this->barcode = $barcode;
		return $this;
	}
	
	/**
	 * @return \Litus\Entity\Cudi\Suppplier
	 */
	public function getSupplier()
	{
		return $this->supplier;
	}
	
	/**
     * @param \Litus\Entity\Cudi\Supplier $supplier
	 *
     * @return \Litus\Entity\Cudi\Articles\Stock
     */
	public function setSupplier($supplier)
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
     * @return \Litus\Entity\Cudi\Articles\Stock
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
     * @return \Litus\Entity\Cudi\Articles\Stock
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
     * @return \Litus\Entity\Cudi\Articles\Stock
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
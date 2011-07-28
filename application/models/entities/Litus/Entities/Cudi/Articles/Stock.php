<?php

namespace Litus\Entities\Cudi\Articles;

/**
 * @MappedSuperclass
 */
use Zend\Di\Exception\UndefinedReferenceException;

abstract class Stock extends \Litus\Entities\Cudi\Article
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
	 * @Column(type="smallint")
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
	 * @TODO OneToOne(targetEntity="Litus\Entities\Cudi\Supplier")
	 */
	private $supplier;
	
	/**
	 * @param string $title The title of the article
	 * @param Litus\Entities\Cudi\Articles\MetaInfo $metaInfo An unlinked metainfo object to link to this article.
	 * @param bigint $purchase_price The purchase price of this article.
	 * @param bigint $sellPrice The sell price of this article for non-members.
	 * @param bigint $sellPriceMembers The sell price of this article for members.
	 * @param smallint $barcode This article's barcode.
	 * @param boolean $bookable Indicates whether the article can be booked.
	 * @param boolean $unbookable Indicates whether the article can be unbooked.
	 * 
	 * @TODO supplier when db is ready
	 * 
	 * @throws InvalidArgumentException If the given meta info object already has an article linked to it.
	 */
	public function __construct($title, $metaInfo, $purchase_price, $sellPrice, $sellPriceMembers, $barcode, $bookable, $unbookable)
	{
		parent::__construct($title, $metaInfo);
		
		$this->purchasePrice = $purchase_price;
		$this->sellPrice = $sellPrice;
		$this->sellPriceMembers = $sellPriceMembers;
		$this->barcode = $barcode;
		$this->bookable = $bookable;
		$this->unbookable = $unbookable;
	}
}

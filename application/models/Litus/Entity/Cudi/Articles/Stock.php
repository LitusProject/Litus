<?php

namespace Litus\Entity\Cudi\Articles;

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
	 * @TODO OneToOne(targetEntity="Litus\Entity\Cudi\Supplier")
	 */
	private $supplier;
}

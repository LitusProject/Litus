<?php

namespace Litus\Entities\Cudi\Articles;

/**
 * @MappedSuperclass
 */
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
}

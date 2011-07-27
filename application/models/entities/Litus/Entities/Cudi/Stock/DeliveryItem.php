<?php

namespace Litus\Entities\Cudi\Stock;

/**
 * @Entity(repositoryClass="Litus\Repositories\Cudi\Stock\DeliveryItemRepository")
 * @Table(name="cudi.stock_deliveryitem")
 */
class DeliveryItem
{
	/**
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;
	
	/**
	 * @ManyToOne(targetEntity="\Litus\Entities\Cudi\Stock\StockItem")
	 * @JoinColumn(name="stockitem_id", referencedColumnName="id")
	 */
	private $stockItem;
	
	/**
	 * @ManyToOne(targetEntity="\Litus\Entities\Cudi\Stock\Delivery")
	 * @JoinColumn(name="delivery_id", referencedColumnName="id")
	 */
	private $delivery;
	
	/**
	 * @Column(type="integer")
	 */
	private $number;
}

<?php

namespace Litus\Entity\Cudi\Stock;

/**
 * @Entity(repositoryClass="Litus\Repository\Cudi\Stock\DeliveryItem")
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
	 * @ManyToOne(targetEntity="\Litus\Entity\Cudi\Stock\StockItem")
	 * @JoinColumn(name="stockitem_id", referencedColumnName="id")
	 */
	private $stockItem;
	
	/**
	 * @ManyToOne(targetEntity="\Litus\Entity\Cudi\Stock\Delivery")
	 * @JoinColumn(name="delivery_id", referencedColumnName="id")
	 */
	private $delivery;
	
	/**
	 * @Column(type="integer")
	 */
	private $number;
}

<?php

namespace Litus\Entity\Cudi\Stock;

/**
 * @Entity(repositoryClass="Litus\Repository\Cudi\Stock\OrderItem")
 * @Table(name="cudi.stock_orderitem")
 */
class OrderItem
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
	 * @ManyToOne(targetEntity="\Litus\Entity\Cudi\Stock\Order", inversedBy="orderItems")
	 * @JoinColumn(name="order_id", referencedColumnName="id")
	 */
	private $order;
	
	/**
	 * @Column(type="integer")
	 */
	private $number;
	
	/**
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * @return \Litus\Entity\Cudi\Stock\StockItem
	 */
	public function getStockItem()
	{
		return $this->stockItem;
	}
	
	/**
	 * @return integer
	 */
	public function getNumber()
	{
		return $this->number;
	}
}

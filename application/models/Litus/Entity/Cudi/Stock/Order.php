<?php

namespace Litus\Entity\Cudi\Stock;

/**
 * @Entity(repositoryClass="Litus\Repository\Cudi\Stock\Order")
 * @Table(name="cudi.stock_order")
 */
class Order
{
	/**
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;
	
	/**
	 * @TODO OneToOne(targetEntity="Litus\Entity\Cudi\Supplier")
	 */
	private $supplier;
	
	/**
	 * @Column(type="datetime")
	 */
	private $date;
	
	/**
	 * @Column(type="float")
	 */
	private $price;
}

<?php

namespace Litus\Entities\Cudi\Stock;

/**
 * @Entity(repositoryClass="Litus\Repositories\Cudi\Stock\Order")
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
	 * @TODO OneToOne(targetEntity="Litus\Entities\Cudi\Supplier")
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

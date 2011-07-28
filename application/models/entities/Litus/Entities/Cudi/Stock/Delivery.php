<?php

namespace Litus\Entities\Cudi\Stock;

/**
 * @Entity(repositoryClass="Litus\Repositories\Cudi\Stock\Delivery")
 * @Table(name="cudi.stock_delivery")
 */
class Delivery
{
	/**
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;
	
	/**
	 * @Column(type="datetime")
	 */
	private $date;
	
	/**
	 * @Column(type="float")
	 */
	private $price;
}

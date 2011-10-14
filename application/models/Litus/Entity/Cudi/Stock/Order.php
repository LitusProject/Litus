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
     * @ManyToOne(targetEntity="Litus\Entity\Cudi\Supplier")
     * @JoinColumn(name="supplier", referencedColumnName="id")
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

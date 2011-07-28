<?php

namespace Litus\Entities\Cudi\Sales;

/**
 * @Entity(repositoryClass="Litus\Repositories\Cudi\Sales\SaleItem")
 * @Table(name="cudi.sales_saleitem")
 */
class SaleItem
{
	/**
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;
	
	/**
	 * @ManyToOne(targetEntity="\Litus\Entities\Cudi\Sales\SalePeriod")
	 * @JoinColumn(name="saleperiod_id", referencedColumnName="id")
	 */
	private $salePeriod;
	
	/**
	 * @ManyToOne(targetEntity="\Litus\Entities\Cudi\Stock\StockItem")
	 * @JoinColumn(name="stockitem_id", referencedColumnName="id")
	 */
	private $stockArticle;
	
	/**
	 * @ManyToOne(targetEntity="\Litus\Entities\Users\Person")
	 * @JoinColumn(name="person_id", referencedColumnName="id")
	 */
	private $person;
	
	/**
	 * @Column(type="datetime")
	 */
	private $timestamp;
	
	/**
	 * @Column(type="float")
	 */
	private $price;
	
	/**
	 * @OneToOne(targetEntity="\Litus\Entities\Cudi\Sales\Booking")
	 * @JoinColumn(name="booking_id", referencedColumnName="id")
	 */
	private $booking;
	
	/**
	 * @TODO
	 */
	private $servingQueueItem;
}

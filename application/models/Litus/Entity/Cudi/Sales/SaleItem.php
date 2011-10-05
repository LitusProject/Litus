<?php

namespace Litus\Entity\Cudi\Sales;

/**
 * @Entity(repositoryClass="Litus\Repository\Cudi\Sales\SaleItem")
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
	 * @ManyToOne(targetEntity="\Litus\Entity\Cudi\Sales\SaleSession")
	 * @JoinColumn(name="salesession_id", referencedColumnName="id")
	 */
	private $saleSession;
	
	/**
	 * @ManyToOne(targetEntity="\Litus\Entity\Cudi\Stock\StockItem")
	 * @JoinColumn(name="stockitem_id", referencedColumnName="id")
	 */
	private $stockArticle;
	
	/**
	 * @ManyToOne(targetEntity="\Litus\Entity\Users\Person")
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
	 * @OneToOne(targetEntity="\Litus\Entity\Cudi\Sales\Booking")
	 * @JoinColumn(name="booking", referencedColumnName="id")
	 */
	private $booking;
	
	/**
	 * @ManyToOne(targetEntity="\Litus\Repository\Cudi\Sales\ServingQueueItem")
	 * @JoinColumn(name="serving_queue_item", referencedColumnName="id")
	 */
	private $servingQueueItem;
}

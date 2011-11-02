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
	 * @ManyToOne(targetEntity="\Litus\Entity\Cudi\Sales\Session")
	 * @JoinColumn(name="session_id", referencedColumnName="id")
	 */
	private $session;
	
	/**
	 * @ManyToOne(targetEntity="\Litus\Entity\Cudi\Article")
	 * @JoinColumn(name="article_id", referencedColumnName="id")
	 */
	private $article;
	
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
	 * @ManyToOne(targetEntity="\Litus\Entity\Cudi\Sales\ServingQueueItem")
	 * @JoinColumn(name="serving_queue_item", referencedColumnName="id")
	 */
	private $servingQueueItem;
}

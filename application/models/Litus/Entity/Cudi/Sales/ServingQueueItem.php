<?php

namespace Litus\Entity\Cudi\Sales;

/**
 * @Entity(repositoryClass="Litus\Repository\Cudi\Sales\ServingQueueItem")
 * @Table(name="cudi.sales_serving_queue_item")
 */
class ServingQueueItem
{
	/**
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;
	
	/**
	 * @ManyToOne(targetEntity="\Litus\Entity\Users\Person")
	 * @JoinColumn(name="person", referencedColumnName="id")
	 */
	private $person;
	
	/**
	 * @TODO
	 */
	private $status;
	
	/**
	 * @ManyToOne(targetEntity="\Litus\Entity\Cudi\Sales\PayDesk")
	 * @JoinColumn(name="pay_desk", referencedColumnName="id")
	 */
	private $payDesk;
	
	/**
	 * @ManyToOne(targetEntity="\Litus\Entity\Cudi\Sales\SaleSession")
	 * @JoinColumn(name="sale_session", referencedColumnName="id")
	 */
	private $saleSession;
}
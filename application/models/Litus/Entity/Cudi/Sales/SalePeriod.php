<?php

namespace Litus\Entity\Cudi\Sales;

/**
 * @Entity(repositoryClass="Litus\Repository\Cudi\Sales\SalePeriod")
 * @Table(name="cudi.sales_saleperiod")
 */
class SalePeriod
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
	private $registerStart;
	
	/**
	 * @Column(type="datetime")
	 */
	private $registerEnd;
	
	/**
	 * @ManyToOne(targetEntity="\Litus\Entity\Users\Person")
	 * @JoinColumn(name="manager_id", referencedColumnName="id")
	 */
	private $manager;

	/**
	 * @Column(type="boolean")
	 */
	private $open;
}

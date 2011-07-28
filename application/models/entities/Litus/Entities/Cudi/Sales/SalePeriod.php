<?php

namespace Litus\Entities\Cudi\Sales;

/**
 * @Entity(repositoryClass="Litus\Repositories\Cudi\Sales\SalePeriod")
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
	 * @ManyToOne(targetEntity="\Litus\Entities\Users\Person")
	 * @JoinColumn(name="manager_id", referencedColumnName="id")
	 */
	private $manager;

	/**
	 * @Column(type="boolean")
	 */
	private $open;
}

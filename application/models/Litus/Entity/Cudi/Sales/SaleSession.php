<?php

namespace Litus\Entity\Cudi\Sales;

/**
 * @Entity(repositoryClass="Litus\Repository\Cudi\Sales\SaleSession")
 * @Table(name="cudi.sales_sale_session")
 */
class SaleSession
{
	/**
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;
	
	/**
	 * @Column(name="open_date", type="datetime")
	 */
	private $openDate;
	
	/**
	 * @Column(name="close_date", type="datetime")
	 */
	private $closeDate;
	
	/**
	 * @Column(name="confirm_date", type="datetime")
	 */
	private $confirmDate;
	
	/**
	 * @Column(name="open_amount", type="float")
	 */
	private $openAmount;
	
	/**
	 * @Column(name="close_amount", type="float")
	 */
	private $closeAmount;
	
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
	 * @todo ManyToOne(targetEntity="Litus\Entity\Unions\Union")
	 */
	private $union;
}
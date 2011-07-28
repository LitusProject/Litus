<?php

namespace Litus\Entities\Cudi;

/**
 * @Entity(repositoryClass="Litus\Repositories\Cudi\SaleSession")
 * @Table(name="cudi.sale_session")
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
	 * @todo ManyToOne(targetEntity="Litus\Entities\Unions\Union")
	 */
	private $union;
}
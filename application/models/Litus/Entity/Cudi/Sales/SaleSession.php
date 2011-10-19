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

	public function setId( $id_ ) {
		$this->id = $id_;
	}

	public function getId() {
		return $this->id;
	}
	
	/**
	 * @Column(name="open_date", type="datetime")
	 */
	private $openDate;

	public function setOpenDate( $openDate_ ) {
		$this->openDate = $openDate_;
	}

	public function getOpenDate() {
		return $this->openDate;
	}
	
	/**
	 * @Column(name="close_date", type="datetime")
	 */
	private $closeDate;

	public function setCloseDate( $closeDate_ ) {
		$this->closeDate = $closeDate_;
	}

	public function getCloseDate() {
		return $this->closeDate;
	}
	
	/**
	 * @Column(name="confirm_date", type="datetime")
	 */
//	private $confirmDate;
	
	/**
	 * @Column(name="open_amount", type="integer")
	 */
	private $openAmount;

	public function setOpenAmount( $openAmount_ ) {
		$this->openAmount = $openAmount_;
	}

	public function getOpenAmount() {
		return $this->openAmount;
	}
	
	/**
	 * @Column(name="close_amount", type="integer")
	 */
	private $closeAmount;

	public function setCloseAmount( $closeAmount_ ) {
		$this->closeAmount = $closeAmount_;
	}

	public function getCloseAmount() {
		return $this->closeAmount;
	}
	
	/**
	 * @Column(type="datetime")
	 */
//	private $registerStart;
	
	/**
	 * @Column(type="datetime")
	 */
//	private $registerEnd;
	
	/**
	 * @ManyToOne(targetEntity="\Litus\Entity\Users\Person")
	 * @JoinColumn(name="manager_id", referencedColumnName="id")
	 */
	private $manager;

	public function setManager( $manager_ ) {
		$this->manager = $manager_;
	}

	public function getManager() {
		return $this->manager;
	}
	
	/**
	 * @todo ManyToOne(targetEntity="Litus\Entity\Unions\Union")
	 */
	//private $union;
}

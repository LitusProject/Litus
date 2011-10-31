<?php

namespace Litus\Entity\Cudi\Sales;

/**
 * @Entity(repositoryClass="Litus\Repository\Cudi\Sales\Session")
 * @Table(name="cudi.sales_session")
 */
class Session
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
	private $openDate;
	
	/**
	 * @Column(type="datetime", nullable=true)
	 */
	private $closeDate;
	
	/**
	 * @OneToOne(targetEntity="\Litus\Entity\Cudi\Sales\CashRegister")
	 * @JoinColumn(name="openAmount", referencedColumnName="id")
	 */
	private $openAmount;
	
	/**
	 * @OneToOne(targetEntity="\Litus\Entity\Cudi\Sales\CashRegister")
	 * @JoinColumn(name="closeAmount", referencedColumnName="id")
	 */
	private $closeAmount;
	
	/**
	 * @ManyToOne(targetEntity="\Litus\Entity\Users\Person")
	 * @JoinColumn(name="manager_id", referencedColumnName="id")
	 */
	private $manager;
	
	/**
	 * @Column(type="string")
	 */
	private $comment;

	/**
	 * @ Column(type="datetime")
	 */
//	private $registerStart;
	
	/**
	 * @ Column(type="datetime")
	 */
//	private $registerEnd;
	
	/**
	 * @todo ManyToOne(targetEntity="Litus\Entity\Unions\Union")
	 */
	//private $union;
	
	public function __construct($openAmount, $comment)
	{
		$this->openDate = new \DateTime();
		$this->openAmount = $openAmount;
		$this->comment = $comment;
	}

	public function getId() {
		return $this->id;
	}

	public function setOpenDate( $openDate ) {
		$this->openDate = $openDate;
		return $this;
	}

	public function getOpenDate() {
		return $this->openDate;
	}

	public function setCloseDate( $closeDate ) {
		$this->closeDate = $closeDate;
		return $this;
	}

	public function getCloseDate() {
		return $this->closeDate;
	}

	public function setOpenAmount( $openAmount ) {
		$this->openAmount = $openAmount;
		return $this;
	}

	public function getOpenAmount() {
		return $this->openAmount;
	}

	public function setCloseAmount( $closeAmount ) {
		$this->closeAmount = $closeAmount;
		return $this;
	}

	public function getCloseAmount() {
		return $this->closeAmount;
	}

	public function setManager( $manager ) {
		$this->manager = $manager;
		return $this;
	}

	public function getManager() {
		return $this->manager;
	}

	public function setComment( $comment ) {
		$this->comment = $comment;
		return $this;
	}

	public function getComment() {
		return $this->comment;
	}
	
	public function isOpen()
	{
		return $this->getCloseDate() === null;
	}
}

<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
 
  // TODO: comments
 
 
namespace CudiBundle\Entity\Sales;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Sales\Session")
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
	 * @Column(name="open_date", type="datetime")
	 */
	private $openDate;
	
	/**
	 * @Column(name="close_date", type="datetime", nullable=true)
	 */
	private $closeDate;
	
	/**
	 * @OneToOne(targetEntity="CommonBundle\Entity\General\Bank\CashRegister")
	 * @JoinColumn(name="open_amount", referencedColumnName="id")
	 */
	private $openAmount;
	
	/**
	 * @OneToOne(targetEntity="CommonBundle\Entity\General\Bank\CashRegister")
	 * @JoinColumn(name="close_amount", referencedColumnName="id")
	 */
	private $closeAmount;
	
	/**
	 * @ManyToOne(targetEntity="CommonBundle\Entity\Users\Person")
	 * @JoinColumn(name="manager", referencedColumnName="id")
	 */
	private $manager;
	
	/**
	 * @Column(type="string")
	 */
	private $comment;
	
	/**
	 * @todo ManyToOne(targetEntity="Litus\Entity\Unions\Union")
	 */
	//private $union;
	
	public function __construct($openAmount, $manager, $comment = '')
	{
		$this->openDate = new \DateTime();
		$this->openAmount = $openAmount;
		$this->comment = $comment;
		$this->manager = $manager;
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
		return null === $this->getCloseDate();
	}
}

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
 
namespace CudiBundle\Entity\Sales;

use CommonBundle\Entity\General\Bank\CashRegister,
	CommonBundle\Entity\Users\Person,
	DateTime;

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
	
	/**
	 * @param CommonBundle\Entity\General\Bank\CashRegister $openAmount The cash register contents at the start of the session
	 * @param CommonBundle\Entity\Users\Person $manager The manager of the session
	 * @param string $comment
	 */
	public function __construct(CashRegister $openAmount, Person $manager, $comment = '')
	{
		$this->openDate = new \DateTime();
		$this->openAmount = $openAmount;
		$this->comment = $comment;
		$this->manager = $manager;
	}

	/**
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * @param DateTime $openDate
	 *
	 * @return CudiBundle\Entity\Sales\Session
	 */
	public function setOpenDate(DateTime $openDate)
	{
		$this->openDate = $openDate;
		return $this;
	}
	/**
	 * @return DateTime
	 */
	public function getOpenDate()
	{
		return $this->openDate;
	}
	
	/**
	 * @param DateTime $closeDate
	 *
	 * @return CudiBundle\Entity\Sales\Session
	 */
	public function setCloseDate($closeDate)
	{
		$this->closeDate = $closeDate;
		return $this;
	}
	
	/**
	 * @return DateTime
	 */
	public function getCloseDate()
	{
		return $this->closeDate;
	}
	
	/**
	 * @return CommonBundle\Entity\General\Bank\CashRegister
	 */
	public function getOpenAmount()
	{
		return $this->openAmount;
	}

	/**
	 * @param CommonBundle\Entity\General\Bank\CashRegister $closeAmount
	 *
	 * @return CudiBundle\Entity\Sales\Session
	 */
	public function setCloseAmount(CashRegister $closeAmount)
	{
		$this->closeAmount = $closeAmount;
		return $this;
	}
	
	/**
	 * @return CommonBundle\Entity\General\Bank\CashRegister $closeAmount
	 */
	public function getCloseAmount()
	{
		return $this->closeAmount;
	}

	/**
	 * @return CommonBundle\Entity\Users\Person
	 */
	public function getManager()
	{
		return $this->manager;
	}
	
	/**
	 * @param string $comment
	 *
	 * @return CudiBundle\Entity\Sales\Session
	 */
	public function setComment($comment)
	{
		$this->comment = $comment;
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getComment() {
		return $this->comment;
	}
	
	/**
	 * @return boolean
	 */
	public function isOpen()
	{
		return null === $this->getCloseDate();
	}
}

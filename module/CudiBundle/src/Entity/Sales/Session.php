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
	DateTime,
	Doctrine\ORM\EntityManager;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Sales\Session")
 * @Table(name="cudi.sales_session")
 */
class Session
{
	/**
	 * @var integer The ID of the sale session
	 *
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;
	
	/**
	 * @var \DateTime The open date of the sale session
	 *
	 * @Column(name="open_date", type="datetime")
	 */
	private $openDate;
	
	/**
	 * @var \DateTime The close date of the sale session
	 *
	 * @Column(name="close_date", type="datetime", nullable=true)
	 */
	private $closeDate;
	
	/**
	 * @var \CommonBundle\Entity\General\Bank\CashRegister The cashregister open status
	 *
	 * @OneToOne(targetEntity="CommonBundle\Entity\General\Bank\CashRegister")
	 * @JoinColumn(name="open_register", referencedColumnName="id")
	 */
	private $openRegister;
	
	/**
	 * @var \CommonBundle\Entity\General\Bank\CashRegister The cashregister close status
	 *
	 * @OneToOne(targetEntity="CommonBundle\Entity\General\Bank\CashRegister")
	 * @JoinColumn(name="close_register", referencedColumnName="id")
	 */
	private $closeRegister;
	
	/**
	 * @var \CommonBundle\Entity\Users\Person The person responsible for the sale session
	 *
	 * @ManyToOne(targetEntity="CommonBundle\Entity\Users\Person")
	 * @JoinColumn(name="manager", referencedColumnName="id")
	 */
	private $manager;
	
	/**
	 * @var string The comments on this sale session
	 *
	 * @Column(type="string")
	 */
	private $comment;
	
	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $_entityManager;
	
	/**
	 * @param \CommonBundle\Entity\General\Bank\CashRegister $openRegister The cash register contents at the start of the session
	 * @param \CommonBundle\Entity\Users\Person $manager The manager of the session
	 * @param string $comment
	 */
	public function __construct(CashRegister $openRegister, Person $manager, $comment = '')
	{
		$this->openDate = new \DateTime();
		$this->openRegister = $openRegister;
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
	 * @param \DateTime $openDate
	 *
	 * @return \CudiBundle\Entity\Sales\Session
	 */
	public function setOpenDate(DateTime $openDate)
	{
		$this->openDate = $openDate;
		return $this;
	}
	/**
	 * @return \DateTime
	 */
	public function getOpenDate()
	{
		return $this->openDate;
	}
	
	/**
	 * @param \DateTime $closeDate
	 *
	 * @return \CudiBundle\Entity\Sales\Session
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
	 * @return \CommonBundle\Entity\General\Bank\CashRegister
	 */
	public function getOpenRegister()
	{
		return $this->openRegister;
	}

	/**
	 * @param \CommonBundle\Entity\General\Bank\CashRegister $closeRegister
	 *
	 * @return \CudiBundle\Entity\Sales\Session
	 */
	public function setCloseRegister(CashRegister $closeRegister)
	{
		$this->closeRegister = $closeRegister;
		return $this;
	}
	
	/**
	 * @return \CommonBundle\Entity\General\Bank\CashRegister
	 */
	public function getCloseRegister()
	{
		return $this->closeRegister;
	}

	/**
	 * @return \CommonBundle\Entity\Users\Person
	 */
	public function getManager()
	{
		return $this->manager;
	}
	
	/**
	 * @param string $comment
	 *
	 * @return \CudiBundle\Entity\Sales\Session
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
	    if(null === $this->getCloseDate())
	        return true;
	    
	    if($this->getCloseDate() >= $this->getOpenDate())
	        return false;
	    
	    return true;
	}
}

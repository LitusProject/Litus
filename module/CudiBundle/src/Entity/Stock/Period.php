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
 
namespace CudiBundle\Entity\Stock;

use CommonBundle\Entity\Users\Person,
    DateTime;

/**
 * @Entity(repositoryClass="CudiBundle\Repository\Stock\Period")
 * @Table(name="cudi.stock_period")
 */
class Period
{
	/**
	 * @var integer The ID of the period
	 *
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;
	
	/**
	 * @var \CommonBundle\Entity\Users\Person The person who created the period
	 *
     * @ManyToOne(targetEntity="CommonBundle\Entity\Users\Person")
     * @JoinColumn(name="person", referencedColumnName="id")
     */
	private $person;
	
	/**
	 * @var \DateTime The start time of the period
	 *
	 * @Column(name="start_date", type="datetime")
	 */
	private $startDate;
	
	/**
	 * @var \DateTime The end time of the period
	 *
	 * @Column(name="end_date", type="datetime", nullable=true)
	 */
	private $endDate;
	
	/**
	 * @param \CommonBundle\Entity\Users\Person $person The person who created the period
	 */
	public function __construct(Person $person)
	{
		$this->person = $person;
		$this->startDate = new DateTime();
	}
	
	/**
	 * Get the id of this period
	 *
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * @return \CommonBundle\Entity\Users\Person
	 */
	public function getPerson()
	{
		return $this->person;
	}
	
	/**
	 * @return \DateTime
	 */
	public function getStartDate()
	{
		return $this->startDate;
	}
	
	/**
	 * @return \DateTime
	 */
	public function getEndDate()
	{
		return $this->endDate;
	}
	
	/**
	 * @return boolean
	 */
	public function isOpen()
	{
	    return $this->endDate == null;
	}
	
	/**
	 * @return \CudiBundle\Entity\Stock\Period
	 */
	public function close()
	{
	    $this->endDate = new DateTime();
	    return $this;
	}
}

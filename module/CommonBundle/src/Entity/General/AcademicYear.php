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
 
namespace CommonBundle\Entity\General;

/**
 * This class represents an academic year entry that is saved in the database
 *
 * @Entity(repositoryClass="CommonBundle\Repository\General\AcademicYear")
 * @Table(name="general.academic_year")
 */
class AcademicYear 
{
    /**
     * @var integer The ID of the address 
     *
     * @Id
     * @GeneratedValue
     * @Column(type="bigint")
     */
    private $id;

    /**
     * @var \DateTime The start date of this academic year
     *
     * @Column(type="datetime")
     */
    private $startDate;

    /**
     * @var \DateTime The end date of this academic year
     *
     * @Column(type="datetime")
     */
    private $endDate;
	
	/**
	 * @param \DateTime $startDate
	 * @param \DateTime $endDate
	 */
	public function __construct($startDate, $endDate)
	{
	    $startDate->setTime(0, 0);
	    $endDate->setTime(0, 0);
	    
	    $this->startDate = $startDate;
	    $this->endDate = $endDate;
	}
	
	/**
	 * @return integer
	 */
    public function getId()
    {
        return $this->id;
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
     * Returns a code representation for the academic year
     *
     * @param boolean $short Whether or not we want a short code
     * @return string
     */
    public function getCode($short = false)
    {
        if (true === $short)
        	return $this->startDate->format('y') . $this->endDate->format('y'); 
        	
		return $this->startDate->format('Y') . '-' . $this->endDate->format('Y'); 
    }
}

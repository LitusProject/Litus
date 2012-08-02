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

use CommonBundle\Component\Util\AcademicYear as AcademicYearUtil;

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
    private $start;

    /**
     * @var \DateTime The end date of this academic year
     *
     * @Column(name="university_start", type="datetime")
     */
    private $universityStart;
    
    /**
     * @param \DateTime $start
     * @param \DateTime $universityStart
     */
    public function __construct($start, $universityStart)
    {
        $start->setTime(0, 0);
        $universityStart->setTime(0, 0);
        
        $this->start = $start;
        $this->universityStart = $universityStart;
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
        return $this->start;
    }
    
    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        $date = clone $this->universityStart;
        return $date->add(
            new DateInterval('P1Y')
        );
    }
        
    /**
     * @return \DateTime
     */
    public function getUniversityStartDate()
    {
        return $this->universityStart;
    }
    
    /**
     * @return \DateTime
     */
    public function getUniversityEndDate()
    {
        return AcademicYearUtil::getEndOfAcademicYear($this->universityStart);
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
            return $this->universityStart->format('y') . $this->getUniversityEndDate()->format('y'); 
            
        return $this->universityStart->format('Y') . '-' . $this->getUniversityEndDate()->format('Y'); 
    }
}

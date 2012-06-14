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
 
namespace SyllabusBundle\Entity;

use CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\Users\People\Academic;

/**
 * @Entity(repositoryClass="SyllabusBundle\Repository\StudentEnrollment")
 * @Table(name="syllabus.student_enrollment")
 */
class StudentEnrollment
{
    /**
     * @var integer The ID of the enrollment
     *
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
    private $id;

	/**
	 * @var \SyllabusBundle\Entity\Subject The subject of the enrollment
	 *
	 * @ManyToOne(targetEntity="SyllabusBundle\Entity\Subject", inversedBy="enrollments")
	 * @JoinColumn(referencedColumnName="id")
	 */
	private $subject;
	
	/**
	 * @var \CommonBundle\Entity\General\AcademicYear The year of the enrollment
	 *
	 * @ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
	 * @JoinColumn(name="academic_year", referencedColumnName="id")
	 */
	private $academicYear;
	
	/**
	 * @var integer The number of students of the enrollment
	 *
	 * @Column(type="integer")
	 */
	private $number;
    
    /**
     * @param \SyllabusBundle\Entity\Subject $subject
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear The year of the mapping
     * @param integer $academicYear The number of students of the enrollment
     */
    public function __construct(Subject $subject, AcademicYear $academicYear, $number)
    {
        $this->subject = $subject;
        $this->academicYear = $academicYear;
        $this->number = $number;
    }
    
    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @return \SyllabusBundle\Entity\Subject
     */
    public function getSubject()
    {
        return $this->subject;
    }
    
    /**
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    public function getAcademicYear()
    {
        return $this->academicYear;
    }
    
    /**
     * @param integer $number
     *
     * @return \SyllabusBundle\Entity\StudentEnrollment
     */
    public function setNumber($number)
    {
        $this->number = $number;
        return $this;
    }
    
    /**
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }
}

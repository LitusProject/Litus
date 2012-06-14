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

use CommonBundle\Entity\General\AcademicYear;

/**
 * @Entity(repositoryClass="SyllabusBundle\Repository\Subject")
 * @Table(name="syllabus.subject")
 */
class Subject
{
	/**
	 * @var integer The ID of the subject
	 *
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
	private $id;

    /**
     * @var string The code of the subject
     *
     * @Column(type="string")
     */
    private $code;

    /**
     * @var string The name of the subject
     *
     * @Column(type="string")
     */
    private $name;

    /**
     * @var integer The semester number of the subject
     *
     * @Column(type="smallint")
     */
    private $semester;
    
    /**
     * @var integer The credits of the subject
     *
     * @Column(type="smallint")
     */
    private $credits;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection The enrollments of the subject
     *
     * @OneToMany(targetEntity="SyllabusBundle\Entity\StudentEnrollment", mappedBy="subject")
     */
    private $enrollments;
    
    /**
     * @param string $code
     * @param string $name
     * @param integer $semester
     * @param integer $credits
     */
    public function __construct($code, $name, $semester, $credits)
    {
        $this->code = $code;
        $this->name = $name;
        $this->semester = $semester;
        $this->credits = $credits;
    }
    
    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * @return integer
     */
    public function getSemester()
    {
        return $this->semester;
    }
    
    /**
     * @return integer
     */
    public function getCredits()
    {
        return $this->credits;
    }
    
    /**
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear
     *
     * @return integer
     */
    public function getNbEnrollment(AcademicYear $academicYear)
    {
        foreach($this->enrollments as $enrollment) {
            if ($enrollment->getAcademicYear() == $academicYear)
                return $enrollment->getNumber();
        }
        return 0;
    }
    
    /**
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear
     *
     * @return \SyllabusBundle\Entity\StudentEnrollment
     */
    public function getEnrollment(AcademicYear $academicYear)
    {
        foreach($this->enrollments as $enrollment) {
            if ($enrollment->getAcademicYear() == $academicYear)
                return $enrollment;
        }
        return null;
    }
}

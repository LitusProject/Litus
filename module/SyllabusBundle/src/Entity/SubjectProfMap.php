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
 * @Entity(repositoryClass="SyllabusBundle\Repository\SubjectProfMap")
 * @Table(name="syllabus.subject_prof_map")
 */
class SubjectProfMap
{
    /**
     * @var integer The ID of the mapping
     *
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
    private $id;

    /**
     * @var \CommonBundle\Entity\Users\People\Academic The prof of the mapping
     *
	 * @ManyToOne(targetEntity="CommonBundle\Entity\Users\People\Academic")
	 * @JoinColumn(referencedColumnName="id")
	 */
	private $prof;

	/**
	 * @var \SyllabusBundle\Entity\Subject The subject of the mapping
	 *
	 * @ManyToOne(targetEntity="SyllabusBundle\Entity\Subject")
	 * @JoinColumn(referencedColumnName="id")
	 */
	private $subject;
	
	/**
	 * @var \CommonBundle\Entity\General\AcademicYear The year of the mapping
	 *
	 * @ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
	 * @JoinColumn(name="academic_year", referencedColumnName="id")
	 */
	private $academicYear;
    
    /**
     * @param \SyllabusBundle\Entity\Subject $subject
     * @param \CommonBundle\Entity\Users\People\Academic $prof
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear The year of the mapping
     */
    public function __construct(Subject $subject, Academic $prof, AcademicYear $academicYear)
    {
        $this->subject = $subject;
        $this->prof = $prof;
        $this->academicYear = $academicYear;
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
     * @return \CommonBundle\Entity\Users\People\Academic
     */
    public function getProf()
    {
        return $this->prof;
    }
    
    /**
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    public function getAcademicYear()
    {
        return $this->academicYear;
    }
}

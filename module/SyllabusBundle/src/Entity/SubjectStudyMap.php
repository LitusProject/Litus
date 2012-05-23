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

/**
 * @Entity(repositoryClass="SyllabusBundle\Repository\StudySubjectMap")
 * @Table(name="syllabus.study_subject_map")
 */
class StudySubjectMap
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
     * @var \SyllabusBundle\Entity\Study The study of the mapping
     *
	 * @ManyToOne(targetEntity="SyllabusBundle\Entity\Study")
	 * @JoinColumn(referencedColumnName="id")
	 */
	private $study;

	/**
	 * @var \SyllabusBundle\Entity\Subject The subject of the mapping
	 *
	 * @ManyToOne(targetEntity="SyllabusBundle\Entity\Subject")
	 * @JoinColumn(referencedColumnName="id")
	 */
	private $subject;

    /**
     * @var boolean Flag whether the subject is mandatory
     *
     * @Column(type="boolean")
     */
    private $mandatory;
    
    /**
     * @var \CommonBundle\Entity\General\AcademicYear The year of the mapping
     *
     * @ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @JoinColumn(name="academic_year", referencedColumnName="id")
     */
    private $academicYear;
    
    /**
     * @param \SyllabusBundle\Entity\Study $study
     * @param \SyllabusBundle\Entity\Subject $subject
     * @param boolean $mandatory
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear The year of the mapping
     */
    public function __construct(Study $study, Subject $subject, $mandatory, AcademicYear $academicYear)
    {
        $this->study = $study;
        $this->subject = $subject;
        $this->mandatory = $mandatory;
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
     * @return \SyllabusBundle\Entity\Study
     */
    public function getStudy()
    {
        return $this->study;
    }
    
    /**
     * @return \SyllabusBundle\Entity\Subject
     */
    public function getSubject()
    {
        return $this->subject;
    }
    
    /**
     * @return boolean
     */
    public function isMandatory()
    {
        return $this->mandatory;
    }
    
    /**
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    public function getAcademicYear()
    {
        return $this->academicYear;
    }
}

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
     * @var integer The ID of this mapping
     *
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
    private $id;

    /**
     * @var \SyllabusBundle\Entity\Study The study of this mapping
     *
	 * @ManyToOne(targetEntity="SyllabusBundle\Entity\Study")
	 * @JoinColumn(referencedColumnName="id")
	 */
	private $study;

	/**
	 * @var \SyllabusBundle\Entity\Subject The subject of this mapping
	 *
	 * @ManyToOne(targetEntity="SyllabusBundle\Entity\Subject")
	 * @JoinColumn(referencedColumnName="id")
	 */
	private $subject;

    /**
     * @var boolean Flag whether this subject is mandatory
     *
     * @Column(type="boolean")
     */
    private $mandatory;
    
    /**
     * @param \SyllabusBundle\Entity\Study $study
     * @param \SyllabusBundle\Entity\Subject $subject
     * @param boolean $mandatory
     */
    public function __construct(Study $study, Subject $subject, $mandatory)
    {
        $this->study = $study;
        $this->subject = $subject;
        $this->mandatory = $mandatory;
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
}

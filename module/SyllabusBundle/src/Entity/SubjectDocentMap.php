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

use CommonBundle\Entity\Users\People\Academic;

/**
 * @Entity(repositoryClass="SyllabusBundle\Repository\SubjectDocentMap")
 * @Table(name="syllabus.subject_docent_map")
 */
class SubjectDocentMap
{
    /**
	 * @Id
	 * @GeneratedValue
	 * @Column(type="bigint")
	 */
    private $id;

    /**
	 * @ManyToOne(targetEntity="CommonBundle\Entity\Users\People\Academic")
	 * @JoinColumn(referencedColumnName="id")
	 */
	private $docent;

	/**
	 * @ManyToOne(targetEntity="SyllabusBundle\Entity\Subject")
	 * @JoinColumn(referencedColumnName="id")
	 */
	private $subject;
    
    /**
     * @param SyllabusBundle\Entity\Subject $subject
     * @param CommonBundle\Entity\Users\People\Academic $docent
     */
    public function __construct(Subject $subject, Academic $docent)
    {
        $this->subject = $subject;
        $this->docent = $docent;
    }
    
    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @return SyllabusBundle\Entity\Subject
     */
    public function getSubject()
    {
        return $this->subject;
    }
    
    /**
     * @return CommonBundle\Entity\Users\People\Academic
     */
    public function getDocent()
    {
        return $this->docent;
    }
}

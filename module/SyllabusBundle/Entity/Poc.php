<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace SyllabusBundle\Entity;

use CommonBundle\Entity\General\AcademicYear,
    Doctrine\ORM\Mapping as ORM,
    SyllabusBundle\Entity\Study\Combination,
    Doctrine\ORM\EntityManager,
	CommonBundle\Entity\User\Person\Academic;
/**
 * @ORM\Entity(repositoryClass="SyllabusBundle\Repository\Poc")
 * @ORM\Table(name="syllabus.pocs")
 */
class Poc 
{
    /**
     * @var integer The ID of the poc
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;
	
	/**
	 * @var group
	 * @ORM\ManyToOne(targetEntity="SyllabusBundle\Entity\Group")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     */
     private $groupId;
  
   /**
     * @var AcademicYear The year of the mapping
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @ORM\JoinColumn(name="academic_year", referencedColumnName="id")
     */
    private $academicYear;

  /**
     * @var Academic The person of the metadata
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person\Academic")
     * @ORM\JoinColumn(name="academic", referencedColumnName="id")
     */
    private $academic;

	
	
	
	
    public function __construct()
    {
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
     /**
     * @return AcademicYear
     */
    public function getAcademicYear()
    {
        return $this->academicYear;
    }

    /**
     * @param  AcademicYear $academicYear
     * @return self
     */
    public function setAcademicYear(AcademicYear $academicYear)
    {
        $this->academicYear = $academicYear;

        return $this;
    }
    
      /**
     * @return Academic
     */
    public function getAcademic()
    {
        return $this->academic;
    }
     /**
     * @param  Academic $academic
     * @return self
     */
    public function setAcademic(Academic $academic)
    {
		$this->academic = $academic;
        
        return $this;
    }
    
    /**
     * @return BigInteger
     */
    public function getGroupId()
    {
        return $this->groupId;
    }
    
    /**
     * @param $group
     * @return self
     */
    public function setGroupId(Group $groupId)
     {
		 $this->groupId = $groupId;
        return $this;
    }
    
    
    
    /**
     * @var EntityManager The EntityManager instance
     */
    protected $entityManager;

    
    /**
     * @param  EntityManager $entityManager
     * @return self
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        return $this;
    }
    
   
   
   
    
}

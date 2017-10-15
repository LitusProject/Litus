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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace SyllabusBundle\Entity;

use CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\User\Person\Academic,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM,
    SyllabusBundle\Entity\Study\Combination;
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

    /**
     * @var boolean whether or not this is just an indicator that there exists a poc group with this group this academic year
     * @ORM\Column(name="indicator", type="boolean",options={"default" : 0})
     */
    private $indicator;

    /**
     * @var the email adress of the poc. Only the indicator email adress is showed in the admin
     *
     * @ORM\Column(type="string",nullable=true)
     */
    private $emailAdress;

    public function __construct()
    {
        $this->indicator = false;
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
     * @return boolean
     */
    public function getIndicator()
    {
        return $this->indicator;
    }

    /**
     * @param $group
     * @return self
     */
    public function setIndicator($indicator)
    {
        $this->indicator = $indicator;

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

    /**
     * @return string
     */
    public function getEmailAdress()
    {
        if ($this->getIndicator()) {
            return $this->emailAdress;
        } else {
            $pocIndicator = $this->entityManager
            ->getRepository('SyllabusBundle\Entity\Poc')
            ->findIndicatorFromGroupAndAcademicYear($this->getGroupId(),$this->getAcademicYear());

            return $pocIndicator->getEmailAdress();
        }
    }

    /**
     * @param  string $name
     * @return self
     */
    public function setEmailAdress($emailAdress)
    {
        $this->emailAdress = $emailAdress;

        return $this;
    }
}

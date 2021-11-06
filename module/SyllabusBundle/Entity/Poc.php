<?php

namespace SyllabusBundle\Entity;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\User\Person\Academic;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="SyllabusBundle\Repository\Poc")
 * @ORM\Table(name="syllabus_pocs")
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
     * @var boolean Whether or not this is just an indicator that there exists a POC group with this group this academic year
     *
     * @ORM\Column(name="indicator", type="boolean", options={"default" : 0})
     */
    private $indicator;

    /**
     * @var EntityManager The EntityManager instance
     */
    protected $entityManager;

    /**
     * @var string The email adress of the POC
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
     * @return integer
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * @param  integer $group
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
     * @param  boolean $indicator
     * @return self
     */
    public function setIndicator($indicator)
    {
        $this->indicator = $indicator;

        return $this;
    }

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
                ->findIndicatorFromGroupAndAcademicYear($this->getGroupId(), $this->getAcademicYear());

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

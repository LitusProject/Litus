<?php

namespace SecretaryBundle\Entity\Organization;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\User\Person\Academic;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="SecretaryBundle\Repository\Organization\MetaData")
 * @ORM\Table(name="users_organizations_metadata")
 */
class MetaData
{
    /**
     * @var integer The ID of the metadata
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Academic The person of the metadata
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person\Academic")
     * @ORM\JoinColumn(name="academic", referencedColumnName="id")
     */
    private $academic;

    /**
     * @var AcademicYear The academic year of the metadata
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @ORM\JoinColumn(name="academic_year", referencedColumnName="id")
     */
    private $academicYear;

    /**
     * @var boolean Whether the academic wants to become a member or not
     *
     * @ORM\Column(name="become_member", type="boolean")
     */
    private $becomeMember;

    /**
     * @param Academic     $academic
     * @param AcademicYear $academicYear
     */
    public function __construct(Academic $academic, AcademicYear $academicYear)
    {
        $this->academic = $academic;
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
     * @return Academic
     */
    public function getAcademic()
    {
        return $this->academic;
    }

    /**
     * @return AcademicYear
     */
    public function getAcademicYear()
    {
        return $this->academicYear;
    }

    /**
     * @return boolean
     */
    public function becomeMember()
    {
        return $this->becomeMember;
    }

    /**
     * @param  boolean $becomeMember
     * @return self
     */
    public function setBecomeMember($becomeMember)
    {
        $this->becomeMember = $becomeMember;

        return $this;
    }
}

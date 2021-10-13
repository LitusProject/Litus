<?php

namespace CommonBundle\Entity\User\Person\Organization;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\General\Organization\Unit;
use Doctrine\ORM\Mapping as ORM;

/**
 * Specifying the mapping between organization and academic.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\User\Person\Organization\UnitMap")
 * @ORM\Table(name="users_people_organizations_units_map")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="inheritance_type", type="string")
 * @ORM\DiscriminatorMap({
 *      "academic"="CommonBundle\Entity\User\Person\Organization\UnitMap\Academic",
 *      "external"="CommonBundle\Entity\User\Person\Organization\UnitMap\External"
 * })
 */
abstract class UnitMap
{
    /**
     * @var integer The ID of this academic year map
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var AcademicYear The academic year
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @ORM\JoinColumn(name="academic_year", referencedColumnName="id")
     */
    private $academicYear;

    /**
     * @var Unit The unit
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Organization\Unit")
     * @ORM\JoinColumn(name="unit", referencedColumnName="id")
     */
    private $unit;

    /**
     * @var boolean Whether or not the academic is the coordinator
     *
     * @ORM\Column(type="boolean")
     */
    private $coordinator;

    /**
     * @var string Extra description of position
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @param AcademicYear $academicYear The academic year
     * @param Unit         $unit         The unit
     * @param boolean      $coordinator  Whether or not the academic is the coordinator
     */
    public function __construct(AcademicYear $academicYear, Unit $unit, $coordinator, $description = '')
    {
        $this->academicYear = $academicYear;
        $this->unit = $unit;
        $this->coordinator = $coordinator;
        $this->description = $description;
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
     * @return Unit
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @return boolean
     */
    public function isCoordinator()
    {
        return $this->coordinator;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description == null ? '' : $this->description;
    }

    /**
     * @param  string $description The unit maps description
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    /**
     * @return string
     */
    abstract public function getFirstName();

    /**
     * @return string
     */
    abstract public function getLastName();

    /**
     * @return string
     */
    abstract public function getPhotoPath();
}

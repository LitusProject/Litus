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

namespace CommonBundle\Entity\User\Person\Organization;

use CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\General\Organization\Unit,
    CommonBundle\Entity\User\Person\Academic,
    Doctrine\ORM\Mapping as ORM;

/**
 * Specifying the mapping between organization and academic.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\User\Person\Organization\UnitMap")
 * @ORM\Table(
 *     name="users.people_organizations_unit_map",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="academics_units_map_unique", columns={"academic", "academic_year", "unit"})}
 * )
 */
class UnitMap
{
    /**
     * @var int The ID of this academic year map
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \CommonBundle\Entity\User\Person\Academic The person
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person\Academic", inversedBy="organizationMap")
     * @ORM\JoinColumn(name="academic", referencedColumnName="id")
     */
    private $academic;

    /**
     * @var \CommonBundle\Entity\General\AcademicYear The academic year
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @ORM\JoinColumn(name="academic_year", referencedColumnName="id")
     */
    private $academicYear;

    /**
     * @var \CommonBundle\Entity\General\Organization\Unit The unit
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
     * @param \CommonBundle\Entity\User\Person\Academic      $person       The person
     * @param \CommonBundle\Entity\General\AcademicYear      $academicYear The academic year
     * @param \CommonBundle\Entity\General\Organization\Unit $unit         The unit
     * @param boolean                                        $coordinator  Whether or not the academic is the coordinator
     */
    public function __construct(Academic $academic, AcademicYear $academicYear, Unit $unit, $coordinator)
    {
        $this->academic = $academic;
        $this->academicYear = $academicYear;
        $this->unit = $unit;
        $this->coordinator = $coordinator;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \CommonBundle\Entity\User\Person\Academic
     */
    public function getAcademic()
    {
        return $this->academic;
    }

    /**
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    public function getAcademicYear()
    {
        return $this->academicYear;
    }

    /**
     * @return \CommonBundle\Entity\General\Organization
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
}

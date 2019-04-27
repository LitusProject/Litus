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

namespace CommonBundle\Entity\User\Person\Organization;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\General\Organization;
use CommonBundle\Entity\User\Person\Academic;
use Doctrine\ORM\Mapping as ORM;

/**
 * Specifying the mapping between organization and academic.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\User\Person\Organization\AcademicYearMap")
 * @ORM\Table(
 *     name="users_people_organizations_academic_years_map",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="users_people_academic_years_map_academic_academic_year", columns={"academic", "academic_year"})}
 * )
 */
class AcademicYearMap
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
     * @var Academic The person
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person\Academic", inversedBy="organizationMap")
     * @ORM\JoinColumn(name="academic", referencedColumnName="id")
     */
    private $academic;

    /**
     * @var AcademicYear The academic year
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @ORM\JoinColumn(name="academic_year", referencedColumnName="id")
     */
    private $academicYear;

    /**
     * @var Organization The organization
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Organization")
     * @ORM\JoinColumn(name="organization", referencedColumnName="id")
     */
    private $organization;

    /**
     * @param Academic     $academic     The person
     * @param AcademicYear $academicYear The academic year
     * @param Organization $organization The organization
     */
    public function __construct(Academic $academic, AcademicYear $academicYear, Organization $organization)
    {
        $this->academic = $academic;
        $this->academicYear = $academicYear;
        $this->organization = $organization;
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
     * @return Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param  Organization $organization
     * @return self
     */
    public function setOrganization(Organization $organization)
    {
        $this->organization = $organization;

        return $this;
    }
}

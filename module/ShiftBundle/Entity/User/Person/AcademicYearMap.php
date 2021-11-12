<?php

namespace ShiftBundle\Entity\User\Person;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\User\Person;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores shift information for a person in a certain academic year.
 *
 * @ORM\Entity(repositoryClass="ShiftBundle\Repository\User\Person\AcademicYearMap")
 * @ORM\Table(
 *      name="shift_users_people_academic_years_map",
 *      uniqueConstraints={@ORM\UniqueConstraint(name="shift_users_academic_years_map_person_academic_year", columns={"person", "academic_year"})})
 */
class AcademicYearMap
{
    /**
     * @var integer The ID of the item
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Person The person associated with this entity
     *
     * @ORM\OneToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var AcademicYear The academic year
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @ORM\JoinColumn(name="academic_year", referencedColumnName="id")
     */
    private $academicYear;

    /**
     * @var boolean Flag whether this person has read the insurance
     *
     * @ORM\Column(name="has_read_insurance", type="boolean")
     */
    private $hasReadInsurance;

    /**
     * @param Person       $person           The person associated with this entity
     * @param AcademicYear $academicYear     The acadmic year when this was read
     * @param boolean      $hasReadInsurance Flag whether this person read the insurance info
     */
    public function __construct(Person $person, AcademicYear $academicYear, $hasReadInsurance)
    {
        $this->person = $person;
        $this->academicYear = $academicYear;
        $this->hasReadInsurance = $hasReadInsurance;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
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
    public function hasReadInsurance()
    {
        return $this->hasReadInsurance;
    }

    /**
     * @param boolean $hasReadInsurance
     *
     * @return self
     */
    public function setHasReadInsurance($hasReadInsurance)
    {
        $this->hasReadInsurance = $hasReadInsurance;

        return $this;
    }
}

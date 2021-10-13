<?php

namespace ShiftBundle\Entity\User\Person;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\User\Person;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores shift information for a person in a certain academic year.
 *
 * @ORM\Entity(repositoryClass="ShiftBundle\Repository\User\Person\RegistrationShiftAcademicYearMap")
 * @ORM\Table(
 *      name="registration_shift_users_people_academic_years_map",
 *      uniqueConstraints={@ORM\UniqueConstraint(name="registration_shift_users_academic_years_map_person_academic_year", columns={"person", "academic_year"})})
 */
class RegistrationShiftAcademicYearMap
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
     * @param Person       $person       The person associated with this entity
     * @param AcademicYear $academicYear The acadmic year when this was read
     */
    public function __construct(Person $person, AcademicYear $academicYear)
    {
        $this->person = $person;
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
}

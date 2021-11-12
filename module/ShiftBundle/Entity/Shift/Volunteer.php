<?php

namespace ShiftBundle\Entity\Shift;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\User\Person;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores a volunteer for a shift.
 *
 * @ORM\Entity(repositoryClass="ShiftBundle\Repository\Shift\Volunteer")
 * @ORM\Table(name="shift_shifts_volunteers")
 */
class Volunteer
{
    /**
     * @var integer The ID of this Volunteer
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var DateTime The signup time
     *
     * @ORM\Column(name="signup_time", type="datetime")
     */
    private $signupTime;

    /**
     * @var Person The person that volunteered
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var boolean Whether or not this volunteer has been payed already
     *
     * @ORM\Column(type="boolean")
     */
    private $payed;

    /**
     * @var AcademicYear the academic year this shift was payed
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @ORM\JoinColumn(name="payed_year", referencedColumnName="id", nullable=true)
     */
    private $payedYear;

    /**
     * @param Person  $person The person that volunteered
     * @param boolean $payed  Whether or not this volunteer has been payed already
     */
    public function __construct(Person $person, $payed = false)
    {
        $this->signupTime = new DateTime();
        $this->person = $person;

        $this->payed = $payed;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return DateTime
     */
    public function getSignupTime()
    {
        return $this->signupTime;
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @return boolean
     */
    public function isPayed()
    {
        return $this->payed;
    }

    /**
     * @param boolean $payed
     * @param AcademicYear|null $academicYear
     * @return self
     */
    public function setPayed($payed, $academicYear = null)
    {
        $this->payed = $payed;
        $this->payedYear = $academicYear;

        return $this;
    }

    /**
     * @return AcademicYear
     */
    public function getPayedYear()
    {
        return $this->payedYear;
    }
}

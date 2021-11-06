<?php

namespace ShiftBundle\Entity\Shift;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\User\Person;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/**
 * This entity stores a responsible for a shift.
 *
 * @ORM\Entity(repositoryClass="ShiftBundle\Repository\Shift\Responsible")
 * @ORM\Table(name="shift_shifts_responsibles")
 */
class Responsible
{
    /**
     * @var integer The ID of this unit
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var DateTime The unit's name
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
     * @param  Person       $person
     * @param  AcademicYear $academicYear
     * @throws InvalidArgumentException
     */
    public function __construct(Person $person, AcademicYear $academicYear)
    {
        $this->signupTime = new DateTime();

        if (!$person->isPraesidium($academicYear)) {
            throw new InvalidArgumentException('The given person cannot be a responsible');
        }

        $this->person = $person;
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
}

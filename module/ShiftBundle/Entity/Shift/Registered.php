<?php

namespace ShiftBundle\Entity\Shift;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\User\Person;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores a registered person for a registration shift.
 *
 * @ORM\Entity(repositoryClass="ShiftBundle\Repository\Shift\Registered")
 * @ORM\Table(name="shift_registration_shifts_registered")
 */
class Registered
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
     * @var string The id to recognize the Registered
     *
     * @ORM\Column(type="string", length=50)
     */
    private $username;

    /**
     * @var string The first name of the registered
     *
     * @ORM\Column(name="first_name",type="string", length=50)
     */
    private $firstName;

    /**
     * @var string The last name of the registered
     *
     * @ORM\Column(name="last_name",type="string", length=50)
     */
    private $lastName;

    /**
     * @var string The email address of the registered
     *
     * @ORM\Column(type="string", length=100)
     */
    private $email;

    /**
     * @var string The ticket code of the registered
     *
     * @ORM\Column(name="ticket_code",type="string", length=100, nullable=true)
     */
    private $ticketCode;


    /**
     * @var boolean If this registered person is a member
     *
     * @ORM\Column(name="member",type="boolean",options={"default" = false})
     */
    private $member;


    /**
     * @var Person The person that registered
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="person",referencedColumnName="id", nullable=true)
     */
    private $person;

    public function __construct(AcademicYear $academicYear, Person $person = null)
    {
        $this->signupTime = new DateTime();

        if ($person !== null) {
            $this->firstName = $person->getFirstName();
            $this->lastName = $person->getLastName();
            $this->username = $person->getUsername();
            $this->email = $person->getEmail();
            $this->person = $person;
            $this->member = $this->person->isMember($academicYear);
        }
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @param  Person $person
     * @return self
     */
    public function setPerson($person)
    {
        $this->person = $person;

        return $this;
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
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param  string $username
     * @return self
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param  string $firstName
     * @return self
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param  string $lastName
     * @return self
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param  string $email
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getTicketCode()
    {
        return $this->ticketCode;
    }

    /**
     * @param  string $ticketCode
     * @return self
     */
    public function setTicketCode($ticketCode)
    {
        $this->ticketCode = $ticketCode;

        return $this;
    }

    /**
     * @return string
     */
    public function isMember()
    {
        return $this->member;
    }

    /**
     * @param  boolean $isMember
     * @return self
     */
    public function setIsMember($isMember)
    {
        $this->member = $isMember;

        return $this;
    }
}

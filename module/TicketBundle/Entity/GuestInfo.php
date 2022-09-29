<?php

namespace TicketBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the guest info item.
 *
 * @ORM\Entity(repositoryClass="TicketBundle\Repository\GuestInfo")
 * @ORM\Table(name="ticket_guests_info")
 */
class GuestInfo
{
    /**
     * @var integer The ID of this guest info
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The first name of this guest
     *
     * @ORM\Column(name="first_name", type="string")
     */
    private $firstName;

    /**
     * @var string The last name of this guest
     *
     * @ORM\Column(name="last_name", type="string")
     */
    private $lastName;

    /**
     * @var string The email address of this guest
     *
     * @ORM\Column(name="email", type="string")
     */
    private $email;

    /**
     * @var string The name of the organization for this guest
     *
     * @ORM\Column(name="organization", type="string", nullable=true)
     */
    private $organization;

    /**
     * @var string The name of the organization for this guest
     *
     * @ORM\Column(name="university_id", type="string", nullable=true)
     */
    private $universityIdentification;

    /**
     * @var string phone number
     *
     * @ORM\Column(name="phone_number", type="string", nullable=true)
     */
    private $phoneNumber;

    /**
     * @var string address
     *
     * @ORM\Column(name="address", type="string", nullable=true)
     */
    private $address;

    /**
     * @var string studies (burgie of archie)
     *
     * @ORM\Column(name="studies", type="string", nullable=true)
     */
    private $studies;

    /**
     * @var string foodOptions (vlees/vegie/vegan)
     *
     * @ORM\Column(name="foodOptions", type="string", nullable=true)
     */
    private $foodOptions;

    /**
     * @var string allergies (vlees/vegie/vegan)
     *
     * @ORM\Column(name="allergies", type="string", nullable=true)
     */
    private $allergies;

    /**
     * @var string transportation (trein/eigen vervoer)
     *
     * @ORM\Column(name="transportation", type="string", nullable=true)
     */
    private $transportation;

    /**
     * @var string comments
     *
     * @ORM\Column(name="comments", type="string", nullable=true)
     */
    private $comments;

    /**
     * @var string The file name of the picture
     *
     * @ORM\Column(name="picture", type="string", unique=true, nullable=true)
     */
    private $picture;

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $organization
     * @param string $universityIdentification
     */
    public function __construct($firstName, $lastName, $email, $organization, $universityIdentification)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->organization = $organization;
        $this->universityIdentification = $universityIdentification;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string firstName
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string lastName
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return string The full name
     */
    public function getFullName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    /**
     * @return string email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @return string getUniversityIdentification (r-number)
     */
    public function getUniversityIdentification()
    {
        return $this->universityIdentification;
    }

    /**
     * @return string phoneNumber
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @return string address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return string studies
     */
    public function getStudies()
    {
        return $this->studies;
    }

    /**
     * @return string foodOptions
     */
    public function getFoodOptions()
    {
        return $this->foodOptions;
    }

    /**
     * @return string allergies
     */
    public function getAllergies()
    {
        return $this->allergies;
    }

    /**
     * @return string transportation
     */
    public function getTransportation()
    {
        return $this->transportation;
    }

    /**
     * @return string comments
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @return string picture
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * @param string picture
     * @return GuestInfo
     */
    public function setPicture(string $picture)
    {
        $this->picture = $picture;

        return $this;
    }
}

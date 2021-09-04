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
     * @ORM\Column(name="organization", type="string")
     */
    private $organization;

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param $organization
     */
    public function __construct($firstName, $lastName, $email, $organization)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
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
}

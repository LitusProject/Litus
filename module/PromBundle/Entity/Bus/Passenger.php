<?php

namespace PromBundle\Entity\Bus;

use Doctrine\ORM\Mapping as ORM;
use PromBundle\Entity\Bus;
use PromBundle\Entity\Bus\ReservationCode;

/**
 * This is the entity for a passenger for the bus
 *
 * @ORM\Entity(repositoryClass="PromBundle\Repository\Bus\Passenger")
 * @ORM\Table(name="prom_buses_passengers")
 */
class Passenger
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
     * @var Bus The first bus, going to the prom
     *
     * @ORM\ManyToOne(targetEntity="PromBundle\Entity\Bus", inversedBy="firstBusSeats")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $firstBus;

    /**
     * @var Bus The second bus, returning from the prom
     *
     * @ORM\ManyToOne(targetEntity="PromBundle\Entity\Bus", inversedBy="secondBusSeats")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $secondBus;

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
     * @var ReservationCode The code used by the passenger.
     *
     * @ORM\ManyToOne(targetEntity="PromBundle\Entity\Bus\ReservationCode")
     * @ORM\JoinColumn(name="code", referencedColumnName="id")
     */
    private $code;

    /**
     * @param string          $firstName
     * @param string          $lastName
     * @param string          $email
     * @param ReservationCode $code
     * @param Bus             $firstBus
     * @param Bus             $secondBus
     */
    public function __construct($firstName, $lastName, $email, ReservationCode $code, $firstBus, $secondBus)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->code = $code;
        $this->firstBus = $firstBus;
        $this->secondBus = $secondBus;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Bus|null
     */
    public function getFirstBus()
    {
        return $this->firstBus;
    }

    /**
     * @param Bus|null
     */
    public function setFirstBus(Bus $bus = null)
    {
        return $this->firstBus = $bus;
    }

    /**
     * @return Bus|null
     */
    public function getSecondBus()
    {
        return $this->secondBus;
    }

    /**
     * @param Bus|null
     */
    public function setSecondBus(Bus $bus = null)
    {
        return $this->secondBus = $bus;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string
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
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return ReservationCode
     */
    public function getCode()
    {
        return $this->code;
    }

    public function removeBus($bus)
    {
        if ($this->firstBus === $bus) {
            $this->firstBus = null;
        }

        if ($this->secondBus === $bus) {
            $this->secondBus = null;
        }
    }
}

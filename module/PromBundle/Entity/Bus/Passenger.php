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
namespace PromBundle\Entity\Bus;

use Doctrine\ORM\Mapping as ORM;
use PromBundle\Entity\Bus;
use PromBundle\Entity\Bus\ReservationCode;

/**
 * This is the entity for a passenger for the bus
 *
 * @ORM\Entity(repositoryClass="PromBundle\Repository\Bus\Passenger")
 * @ORM\Table(name="prom.bus_passenger")
 */
class Passenger
{
    /**
     * @var int The ID of this guest info
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
     * @return int
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
}

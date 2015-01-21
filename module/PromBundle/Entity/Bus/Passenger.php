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
 *
 * @license http://litus.cc/LICENSE
 */
namespace PromBundle\Entity\Bus;

use Doctrine\ORM\Mapping as ORM,
    PromBundle\Entity\Bus;

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
     * @var Subject The subject of the enrollment
     *
     * @ORM\ManyToOne(targetEntity="PromBundle\Entity\Bus", inversedBy="seats")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $bus;

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
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $code
     */
    public function __construct(Bus $bus, $firstName, $lastName, $email, $code)
    {
        $this->bus = $bus;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->code = $code;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return PromBundle\Entity\Bus
     */
    public function getBus()
    {
        return $this->bus;
    }

    /**
     * @param PromBundle\Entity\Bus
     */
    public function setBus(Bus $bus = null)
    {
        return $this->bus = $bus;
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
     * @return string code
     */
    public function getCode()
    {
        return $this->code;
    }
}

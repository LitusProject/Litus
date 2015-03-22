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
namespace PromBundle\Entity;

use DateTime,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM,
    Exception,
    PromBundle\Entity\Bus\Passenger;

/**
 * This is the entity for a bus
 *
 * @ORM\Entity(repositoryClass="PromBundle\Repository\Bus")
 * @ORM\Table(name="prom.bus")
 */
class Bus
{
    /**
     * @var integer The ID of this article
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var DateTime The depature time of this bus
     *
     * @ORM\Column(name="departure_time", type="datetime")
     */
    private $departureTime;

    /**
     * @var int The amount seats in total.
     *
     * @ORM\Column(type="integer")
     */
    private $totalSeats;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="PromBundle\Entity\Bus\Passenger", mappedBy="firstBus")
     */
    private $firstBusSeats;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="PromBundle\Entity\Bus\Passenger", mappedBy="secondBus")
     */
    private $secondBusSeats;

    /**
     * @var string|null first or second ('Go' or 'Return')
     *
     * @ORM\Column(type="string", length=6, nullable=true)
     */
    private $direction;

    /**
     * @var int The maximum amount of passengers available on buses
     */
    private $maxPassengerNb = 100;

    /**
     * Creates a new Bus with the given attributes
     */
    public function __construct()
    {
        $this->seats = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * @param string $direction The direction in which the bus is going
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;
    }

    /**
     * @return DateTime
     */
    public function getDepartureTime()
    {
        return $this->departureTime;
    }

    /**
     * Set the departureTime
     *
     * @param DateTime $time The departure time
     */
    public function setDepartureTime(DateTime $time)
    {
        $this->departureTime = $time;
    }

    /**
     * @return int
     */
    public function getTotalSeats()
    {
        return $this->totalSeats;
    }

    /**
     * @param int $totalSeats The total amount of seats
     */
    public function setTotalSeats($totalSeats)
    {
        if (!$this->isValidPassengerAmount($totalSeats)) {
            return;
        }

        $this->totalSeats = $totalSeats;
    }

    /**
     * @param  Passenger $passenger
     * @return self
     */
    public function addPassenger(Passenger $passenger)
    {
        if ($this->getDirection() == 'Go') {
            $this->firstBusSeats->add($passenger);
        } else {
            $this->secondBusSeats->add($passenger);
        }

        return $this;
    }

    /**
     * @param  Passenger $passenger
     * @return self
     */
    public function removePassenger(Passenger $passenger)
    {
        if ($this->getDirection() == 'Go') {
            $this->firstBusSeats->remove($passenger);
        } else {
            $this->secondBusSeats->remove($passenger);
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getReservedSeats()
    {
        return $this->firstBusSeats->count() + $this->secondBusSeats->count();
    }

    /**
     * @return array
     */
    public function getReservedSeatsArray()
    {
        return $this->firstBusSeats->toArray() + $this->secondBusSeats->toArray();
    }

    /**
     * @param  int     $passengerAmount
     * @return boolean
     */
    private function isValidPassengerAmount($passengerAmount)
    {
        if ($passengerAmount > $this->maxPassengerNb) {
            throw new Exception('The passenger amount is exceeding ' . $this->maxPassengerNb);
        }

        return true;
    }
}

<?php

namespace PromBundle\Entity;

use CommonBundle\Entity\General\AcademicYear;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use PromBundle\Entity\Bus\Passenger;

/**
 * This is the entity for a bus
 *
 * @ORM\Entity(repositoryClass="PromBundle\Repository\Bus")
 * @ORM\Table(name="prom_buses")
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
     * @var AcademicYear The shift's academic year
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @ORM\JoinColumn(name="academic_year", referencedColumnName="id")
     */
    private $academicYear;

    /**
     * @var DateTime The depature time of this bus
     *
     * @ORM\Column(name="departure_time", type="datetime")
     */
    private $departureTime;

    /**
     * @var integer The amount seats in total.
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
     * @var integer The maximum amount of passengers available on buses
     */
    private $maxPassengerNb = 100;

    /**
     * @var string name of the bus
     @ORM\Column(type="string", length=20, nullable=true)
     */
    private $name;

    /**
     * Creates a new Bus with the given attributes
     */
    public function __construct(AcademicYear $academicYear)
    {
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
     * @return AcademicYear
     */
    public function getAcademicYear()
    {
        return $this->academicYear;
    }

    /**
     * @return string|null
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
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

     /**
     * Set the bus name
     *
     * @param string $name of the bus
     */
    public function setName($name)
    {
        return $this->name = $name;
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
     * @return integer
     */
    public function getTotalSeats()
    {
        return $this->totalSeats;
    }

    /**
     * @param integer $totalSeats The total amount of seats
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
            $this->firstBusSeats->removeElement($passenger);
        } else {
            $this->secondBusSeats->removeElement($passenger);
        }

        return $this;
    }

    /**
     * @return integer
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
     * @param  integer $passengerAmount
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

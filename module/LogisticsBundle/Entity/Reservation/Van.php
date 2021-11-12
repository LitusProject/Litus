<?php

namespace LogisticsBundle\Entity\Reservation;

use CommonBundle\Entity\User\Person;
use Doctrine\ORM\Mapping as ORM;
use LogisticsBundle\Entity\Driver;

/**
 * This is the entity for a reservation.
 *
 * A reservation is associated with a certain resource and locks it from a given start date to a given end date.
 *
 * TOOD: Refactor to subclasses for van, car, and bike.
 *
 * @ORM\Entity(repositoryClass="LogisticsBundle\Repository\Reservation\Van")
 * @ORM\Table(name="logistics_reservations_van")
 */
class Van extends \LogisticsBundle\Entity\Reservation
{
    const RESOURCE_NAME = 'Van';

    /**
     * @var Driver The driver of the van for this reservation.
     *
     * @ORM\ManyToOne(targetEntity="LogisticsBundle\Entity\Driver")
     * @ORM\JoinColumn(name="driver_id", referencedColumnName="person", nullable=true)
     */
    private $driver;

    /**
     * @var Person The passenger of the van for this reservation.
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person\Academic", cascade={"persist"})
     * @ORM\JoinColumn(name="passenger", referencedColumnName="id", nullable=true)
     */
    private $passenger;

    /**
     * @var string The load of the van for this reservation, i.e. what needs to be transported.
     *
     * @ORM\Column(type="text")
     */
    private $load;

    /**
     * @var string Y or N to indicate whether this is a car reservation, boolean could not be used for unkown reasons
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $car;

    /**
     *@var string Y or N to indicate whether this is a bike reservation, boolean could not be used for unknown reasons
     *
     *@ORM\Column(type="text", nullable=true)
     */
    private $bike;

    /**
     * @return Driver
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @param  Driver|null $driver
     * @return self
     */
    public function setDriver(Driver $driver = null)
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * @return Person
     */
    public function getPassenger()
    {
        return $this->passenger;
    }

    /**
     * @param  Person $passenger
     * @return self
     */
    public function setPassenger(Person $passenger)
    {
        $this->passenger = $passenger;

        return $this;
    }

    /**
     * @return string
     */
    public function getLoad()
    {
        return $this->load;
    }

    /**
     * @param  string $load
     * @return self
     */
    public function setLoad($load)
    {
        $this->load = $load;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getCar()
    {
        return $this->car;
    }

    /**
     * @param  boolean $car
     * @return self
     */
    public function setCar($car)
    {
        $this->car = $car;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getBike()
    {
        return $this->bike;
    }

    /**
     * @param  boolean $bike
     * @return self
     */
    public function setBike($bike)
    {
        $this->bike = $bike;

        return $this;
    }
}

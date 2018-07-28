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

namespace LogisticsBundle\Entity\Reservation;

use CommonBundle\Entity\User\Person,
    Doctrine\ORM\Mapping as ORM,
    LogisticsBundle\Entity\Driver;

/**
 * This is the entity for a reservation.
 *
 * A reservation is associated with a certain resource and locks it from a given start date to a given end date.
 *
 * @ORM\Entity(repositoryClass="LogisticsBundle\Repository\Reservation\VanReservation")
 * @ORM\Table(name="logistics.reservations_van")
 */
class VanReservation extends Reservation
{
    const VAN_RESOURCE_NAME = 'Van';

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
     * @var text Y or N to indicate whether this is a car reservation, boolean could not be used for unkown reasons
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $car;

    /**
     *@var text Y or N to indicate whether this is a bike reservation, boolean could not be used for unknown reasons
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

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

namespace LogisticsBundle\Entity\Reservation;

use CommonBundle\Entity\User\Person,
    DateTime,
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
     * @param DateTime           $startDate
     * @param DateTime           $endDate
     * @param string             $reason
     * @param string             $load
     * @param ReservableResource $resource
     * @param string             $additionalInfo
     * @param Person             $creator
     */
    public function __construct(DateTime $startDate, DateTime $endDate, $reason, $load, ReservableResource $resource, $additionalInfo, Person $creator)
    {
        parent::__construct($startDate, $endDate, $reason, $resource, $additionalInfo, $creator);

        $this->driver = null;
        $this->passenger = null;
        $this->load = $load;
    }

    /**
     * @return Person
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

}

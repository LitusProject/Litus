<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
namespace LogisticsBundle\Entity\Reservation;

use Doctrine\ORM\Mapping as ORM;

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
     * @var The driver of the van for this reservation.
     *
     * @ORM\ManyToOne(targetEntity="LogisticsBundle\Entity\Driver")
     * @ORM\JoinColumn(name="driver_id", referencedColumnName="person", nullable=true)
     */
    private $driver;

    /**
     * @var The passenger of the van for this reservation.
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\Users\People\Academic", cascade={"persist"})
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
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param string $reason
     * @param string $load
     * @param \LogisticsBundle\Entity\Reservation\ReservableResource $resource
     * @param string $additionalInfo
     * @param \CommonBundle\Entity\Users\Person $creator
     */
    public function __construct($startDate, $endDate, $reason, $load, ReservableResource $resource, $additionalInfo, $creator) {
        parent::__construct($startDate, $endDate, $reason, $resource, $additionalInfo, $creator);

        $this->driver = null;
        $this->passenger = null;
        $this->load = $load;
    }

    /**
     * @return \CommonBundle\Entity\Users\Person
     */
    public function getDriver() {
        return $this->driver;
    }

    /**
     * @param \CommonBundle\Entity\Users\Person $driver
     *
     * @return \LogisticsBundle\Entity\Reservation\VanReservation
     */
    public function setDriver($driver) {
        $this->driver = $driver;
        return $this;
    }

    /**
     * @return \CommonBundle\Entity\Users\Person
     */
    public function getPassenger() {
        return $this->passenger;
    }

    /**
     * @param \CommonBundle\Entity\Users\Person $passenger
     *
     * @return \LogisticsBundle\Entity\Reservation\VanReservation
     */
    public function setPassenger($passenger) {
        $this->passenger = $passenger;
        return $this;
    }

    /**
     * @return string
     */
    public function getLoad() {
        return $this->load;
    }

    /**
     * @param string $load
     *
     * @return \LogisticsBundle\Entity\Reservation\VanReservation
     */
    public function setLoad($load) {
        $this->load = $load;
        return $this;
    }

}

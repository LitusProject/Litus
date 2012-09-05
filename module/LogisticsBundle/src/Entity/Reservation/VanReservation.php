<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
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
 * @ORM\Table(name="logistics.reservation_van")
 */
class VanReservation extends Reservation
{
    
    /**
     * @var The reservation's unique identifier
     *
     * @ORM\ManyToOne(targetEntity="LogisticsBundle\Entity\Driver")
     * @ORM\JoinColumn(name="driver_id", referencedColumnName="person", nullable=true)
     */
    private $driver;
    
    public function __construct($startDate, $endDate, $reason, ReservableResource $resource, $additionalInfo, $creator) {
        parent::__construct($startDate, $endDate, $reason, $resource, $additionalInfo, $creator);
        $this->driver = null;
    }
    
    public function getDriver() {
        return $this->driver;
    }
    
    public function setDriver($driver) {
        $this->driver = $driver;
        return $this;
    }
    
}
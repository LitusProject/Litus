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

use CommonBundle\Entity\User\Person,
    DateTime,
    Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for a reservation.
 *
 * A reservation is associated with a certain resource and locks it from a given start date to a given end date.
 *
 * @ORM\Entity(repositoryClass="LogisticsBundle\Repository\Reservation\PianoReservation")
 * @ORM\Table(name="logistics.reservations_piano")
 */
class PianoReservation extends Reservation
{

    const PIANO_RESOURCE_NAME = 'Piano';

    /**
     * @var The driver of the van for this reservation.
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="player", referencedColumnName="id")
     */
    private $player;

    /**
     * @var Flag whether this reservation is confirmed
     *
     * @ORM\Column(type="boolean")
     */
    private $confirmed;

    /**
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param \LogisticsBundle\Entity\Reservation\ReservableResource $resource
     * @param string $additionalInfo
     * @param \CommonBundle\Entity\User\Person $creator
     * @param \CommonBundle\Entity\User\Person $player
     */
    public function __construct(DateTime $startDate, DateTime $endDate, ReservableResource $resource, $additionalInfo, Person $creator, Person $player)
    {
        parent::__construct($startDate, $endDate, '', $resource, $additionalInfo, $creator);

        $this->player = $player;
        $this->confirmed = false;
    }

    /**
     * @return \CommonBundle\Entity\User\Person
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * @param \CommonBundle\Entity\User\Person $player
     * @return \LogisticsBundle\Entity\Reservation\PianoReservation
     */
    public function setPlayer(Person $player)
    {
        $this->player = $player;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isConfirmed()
    {
        return $this->confirmed;
    }

    /**
     * @param boolean $confirmed
     * @return \LogisticsBundle\Entity\Reservation\PianoReservation
     */
    public function setConfirmed($confirmed = true)
    {
        $this->confirmed = $confirmed;
        return $this;
    }
}
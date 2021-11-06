<?php

namespace LogisticsBundle\Entity\Reservation;

use CommonBundle\Entity\User\Person;
use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for a reservation.
 *
 * A reservation is associated with a certain resource and locks it from a given start date to a given end date.
 *
 * @ORM\Entity(repositoryClass="LogisticsBundle\Repository\Reservation\Piano")
 * @ORM\Table(name="logistics_reservations_piano")
 */
class Piano extends \LogisticsBundle\Entity\Reservation
{
    const RESOURCE_NAME = 'Piano';

    /**
     * @var Person The driver of the van for this reservation.
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="player", referencedColumnName="id")
     */
    private $player;

    /**
     * @var boolean Flag whether this reservation is confirmed
     *
     * @ORM\Column(type="boolean")
     */
    private $confirmed;

    /**
     * @param resource $resource
     * @param Person   $creator
     */
    public function __construct(Resource $resource, Person $creator)
    {
        parent::__construct($resource, $creator);

        $this->confirmed = false;
        $this->setReason('');
    }

    /**
     * @return Person
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * @param  Person $player
     * @return self
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
     * @param  boolean $confirmed
     * @return self
     */
    public function setConfirmed($confirmed = true)
    {
        $this->confirmed = $confirmed;

        return $this;
    }
}

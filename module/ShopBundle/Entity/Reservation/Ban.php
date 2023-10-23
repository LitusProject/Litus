<?php

namespace ShopBundle\Entity\Reservation;

use DateTime;
use CommonBundle\Entity\User\Person;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores a reservation ban for a user.
 *
 * @ORM\Entity(repositoryClass="ShopBundle\Repository\Reservation\Ban")
 * @ORM\Table(name="shop_reservations_bans")
 */
class Ban
{
    /**
     * @var integer The ID of this ban
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Person The person this ban belongs to
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var DateTime The timestamp at which the ban starts
     *
     *
     */
    private $startTimestamp;

    /**
     * @var DateTime The timestamp at which the ban ends (null if it does not end)
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endTimestamp;

    /**
     * @return Person The person this ban applies to
     */
    public function GetPerson() {
        return $this->person;
    }

    /**
     * @param Person $person The person this ban belongs to
     * @param DateTime $startTimestamp The timestamp at which the ban starts
     * @param DateTime|null $endTimestamp The timestamp at which the ban ends (null if it does not end)
     */
    public function __construct(Person $person, DateTime $startTimestamp, DateTime $endTimestamp=null)
    {
        $this->person = $person;
        $this->startTimestamp = $startTimestamp;
        $this->endTimestamp = $endTimestamp;
    }

    /**
     * @param $person
     * @return $this
     */
    public function SetPerson($person) {
        $this->person = $person;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStartTimestamp() {
        return $this->startTimestamp;
    }

    /**
     * @param $timestamp
     * @return $this
     */
    public function setStartTimestamp($timestamp) {
        $this->startTimestamp = $timestamp;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getEndTimestamp() {
        return $this->endTimestamp;
    }

    /**
     * @param $timestamp
     * @return $this
     */
    public function setEndTimestamp($timestamp) {
        $this->endTimestamp = $timestamp;

        return $this;
    }

    /**
     * Set end timestamp to null so that ban will never end
     *
     * @return $this
     */
    public function removeEndTimestamp() {
        $this->endTimestamp = null;

        return $this;
    }

    /**
     * @return bool Whether the ban is currently active on the user
     */
    public function IsActive() {
        // Get the current timestamp
        $currentTimestamp = time();

        if ($currentTimestamp >= $this->startTimestamp) {
            if ($this->endTimestamp === null) {
                return true; // User is banned indefinitely
            }

            if ($currentTimestamp <= $this->endTimestamp) {
                return true; // User is currently banned
            }
        }

        return false;
    }
}
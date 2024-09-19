<?php

namespace ShopBundle\Entity\Reservation;

use CommonBundle\Entity\User\Person;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use ShopBundle\Entity\Session;

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
     * @ORM\Column(type="datetime")
     */
    private $startTimestamp;

    /**
     * @var DateTime The timestamp at which the ban ends (null if it does not end)
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endTimestamp;

    /**
     * @var Session The sales session in case the ban has been created from a no-show, null otherwise
     *
     * @ORM\ManyToOne(targetEntity="ShopBundle\Entity\Session", cascade={"persist"})
     * @ORM\JoinColumn(name="sales_session", referencedColumnName="id", nullable=true)
     */
    private $salesSession;

    public function __construct()
    {
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Person The person this ban applies to
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @param $person
     * @return $this
     */
    public function setPerson($person)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStartTimestamp()
    {
        return $this->startTimestamp;
    }

    /**
     * @param $timestamp
     * @return $this
     */
    public function setStartTimestamp($timestamp)
    {
        $this->startTimestamp = $timestamp;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getEndTimestamp()
    {
        return $this->endTimestamp;
    }

    /**
     * @param $timestamp
     * @return $this
     */
    public function setEndTimestamp($timestamp)
    {
        $this->endTimestamp = $timestamp;

        return $this;
    }

    /**
     * Set end timestamp to null so that ban will never end
     *
     * @return $this
     */
    public function removeEndTimestamp()
    {
        $this->endTimestamp = null;

        return $this;
    }

    /**
     * @return Session
     */
    public function getSalesSession()
    {
        return $this->salesSession;
    }

    /**
     * @param Session $salesSession
     * @return $this
     */
    public function setSalesSession(Session $salesSession)
    {
        $this->salesSession = $salesSession;

        return $this;
    }

    /**
     * @return boolean Whether the ban is currently active on the user
     */
    public function isActive()
    {
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

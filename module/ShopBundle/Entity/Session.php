<?php

namespace ShopBundle\Entity;

use CommonBundle\Entity\User\Person;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use ShopBundle\Entity\Reservation\Ban;

/**
 * This entity stores a sales session.
 *
 * @ORM\Entity(repositoryClass="ShopBundle\Repository\Session")
 * @ORM\Table(name="shop_sessions")
 */
class Session
{
    /**
     * @var integer The ID of this sales session
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var DateTime The start date of this sales session
     *
     * @ORM\Column(type="datetime")
     */
    private $startDate;

    /**
     * @var DateTime The end date of this sales session
     *
     * @ORM\Column(type="datetime")
     */
    private $endDate;

    /**
     * @var DateTime|null The end date for reservations for this sales session
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $finalReservationDate;

    /**
     * @var boolean Whether reservations can be made for this sales session
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $reservationsPossible;

    /**
     * @var string Remarks for this sales session
     *
     * @ORM\Column(type="text")
     */
    private $remarks;

    /**
     * @var boolean Whether there are already people selected who will get reward
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $reward;

    /**
     * @var integer Amount of rewards that will be given this session
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $amountRewards;

    /**
     * @var ArrayCollection All the bans that have been set as a result of no-shows in this sales session.
     *
     * @ORM\OneToMany(targetEntity="ShopBundle\Entity\Reservation\Ban", mappedBy="salesSession", cascade={"persist"}, orphanRemoval=true)
     */
    private $bans;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    // TODO: Rename to SaleSession
    // TODO: Add __construct()

    /**
     * @param  DateTime $startDate
     * @return self
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param  DateTime $endDate
     * @return self
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param  DateTime $finalReservationDate
     * @return self
     */
    public function setFinalReservationDate($finalReservationDate)
    {
        $this->finalReservationDate = $finalReservationDate;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getFinalReservationDate()
    {
        return $this->finalReservationDate;
    }

    /**
     * @param  boolean $reservationsPossible
     * @return self
     */
    public function setReservationsPossible($reservationsPossible)
    {
        $this->reservationsPossible = $reservationsPossible;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getReservationsPossible()
    {
        return $this->reservationsPossible;
    }

    /**
     * @param  boolean $reward
     * @return self
     */
    public function setReward($reward)
    {
        $this->reward = $reward;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getReward()
    {
        return $this->reward;
    }

    /**
     * @param  integer $amountRewards
     * @return self
     */
    public function setAmountRewards($amountRewards)
    {
        $this->amountRewards = $amountRewards;

        return $this;
    }

    /**
     * @return integer
     */
    public function getAmountRewards()
    {
        return $this->amountRewards;
    }

    /**
     * @param  string $remarks
     * @return self
     */
    public function setRemarks($remarks)
    {
        $this->remarks = $remarks;

        return $this;
    }

    /**
     * @return string
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     * @param Ban $ban
     * @return $this
     */
    public function addBan(Ban $ban) {
        $this->bans->add($ban);
    }

    /**
     * Check if a Person has received a ban already in this sales session.
     *
     * @param Person $person
     * @return boolean
     */
    public function containsBanForPerson(Person $person) {
        foreach ($this->bans as $ban) {
            if ($ban->getPerson() == $person) {
                return true;
            }
        }
        return false;
    }

    /**
     * Removes all the bans related to a certain person that have been made in this sales session.
     * If there are no bans present, nothing happens.
     *
     * @param Person $person
     * @return void
     */
    public function removeAllBansFromPerson(Person $person) {
        $elementsToRemove = [];

        foreach ($this->bans as $ban) {
            if ($ban->getPerson() == $person) {
                $elementsToRemove[] = $ban;
            }
        }

        foreach ($elementsToRemove as $banToRemove) {
            $this->bans->removeElement($banToRemove);
        }
    }
}

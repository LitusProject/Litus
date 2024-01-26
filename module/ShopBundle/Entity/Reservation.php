<?php

namespace ShopBundle\Entity;

use CommonBundle\Entity\User\Person;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use ShopBundle\Entity\Session as SalesSession;

/**
 * This entity stores a reservation.
 *
 * @ORM\Entity(repositoryClass="ShopBundle\Repository\Reservation")
 * @ORM\Table(name="shop_reservations")
 */
class Reservation
{
    /**
     * @var integer The ID of this reservation
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Product The product of this reservation
     *
     * @ORM\ManyToOne(targetEntity="ShopBundle\Entity\Product")
     * @ORM\JoinColumn(name="product", referencedColumnName="id")
     */
    private $product;

    /**
     * @var integer The amount of products reserved
     *
     * @ORM\Column(type="bigint")
     */
    private $amount;

    /**
     * @var SalesSession The id of the sales session for which this reservation was made
     *
     * @ORM\ManyToOne(targetEntity="ShopBundle\Entity\Session")
     * @ORM\JoinColumn(name="session", referencedColumnName="id")
     */
    private $salesSession;

    /**
     * @var Person The person who made the reservation
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var DateTime The date this reservation was made on
     *
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

    /**
     * @var boolean If a person already got his sandwich
     *
     * @ORM\Column(name="consumed", type="boolean", nullable=true)
     */
    private $consumed;

    /**
     * @var boolean If a person already got his sandwich
     *
     * @ORM\Column(name="reward", type="boolean", nullable=true)
     */
    private $reward;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param  Product $product
     * @return self
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param  SalesSession $salesSession
     * @return self
     */
    public function setSalesSession(SalesSession $salesSession)
    {
        $this->salesSession = $salesSession;

        return $this;
    }

    /**
     * @return SalesSession
     */
    public function getSalesSession()
    {
        return $this->salesSession;
    }

    /**
     * @param  integer $amount
     * @return self
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return integer
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param  Person $person
     * @return self
     */
    public function setPerson(Person $person)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @param  DateTime $timestamp
     * @return self
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param  boolean $consumed
     * @return self
     */
    public function setConsumed($consumed)
    {
        $this->consumed = $consumed;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getConsumed()
    {
        return $this->consumed;
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
     * @return boolean
     */
    public function canCancel()
    {
        $timestamp = new DateTime();

        return $timestamp < $this->getSalesSession()->getFinalReservationDate();
    }
}

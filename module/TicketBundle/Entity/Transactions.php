<?php

namespace TicketBundle\Entity;
use DateTime;
use CommonBundle\Entity\User\Person;
use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for consumption transactions
 *
 * @ORM\Entity(repositoryClass="TicketBundle\Repository\Transactions")
 * @ORM\Table(name="ticket_transactions")))
 */
class Transactions
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var \CommonBundle\Entity\User\Person The person to whom the consumptions belong
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person", cascade={"persist"})
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var \CommonBundle\Entity\User\Person The person to whom the consumptions belong
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person", cascade={"persist"})
     * @ORM\JoinColumn(name="owner", referencedColumnName="id")
     */
    private $owner;

    /**
     * @var integer
     *
     * @ORM\Column(name="amount", type="integer", nullable=true)
     */
    private $amount;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="time", type="datetime")
     */
    private $time;

    /**
     * Transactions constructor.
     * @param int $amount
     * @param \TicketBundle\Entity\Consumptions $consumption
     * @param Person $person
     */
    public function __construct(int $amount, Person $owner, Person $person = null)
    {
        $this->amount = $amount;
        $this->owner = $owner;
        $this->person = $person;
        $this->time = new DateTime();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @return integer
     */
    public function getAmount()
    {
        return $this->amount;
    }

    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @return DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param Person|null $person
     * @return Transactions
     */
    public function setPerson(Person $person = null)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * @param integer $amount
     * @return Transactions
     */
    public function setAmount(int $amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @param Person $owner
     * @return Transactions
     */
    public function setConsumption(Person $owner)
    {
        $this->owner = $owner;

        return $this;
    }
}
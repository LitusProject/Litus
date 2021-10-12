<?php

namespace TicketBundle\Entity;

use CommonBundle\Entity\User\Person;
use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for the consumptions
 *
 * @ORM\Entity(repositoryClass="TicketBundle\Repository\Consumptions")
 * @ORM\Table(name="ticket_consumptions")
 */
class Consumptions
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
     * @var integer the amount of consumptions for the academic
     *
     * @ORM\Column(name="number_of_consumptions", type="integer", nullable=true)
     */
    private $number_of_consumptions;

    /**
     * @var string The consumptions owner username
     *
     * @ORM\Column(name="username", type="string", length=50)
     */
    private $username;

    /**
     * @var string The consumptions owner full name
     *
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;

    /**
     * Consumptions constructor.
     */
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
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @return integer
     */
    public function getConsumptions()
    {
        return $this->number_of_consumptions;
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->name;
    }

    /**
     * @param Person|null $person
     * @return Consumptions
     */
    public function setPerson(Person $person = null)
    {
        $this->person = $person;
        $this->name = $person->getFullName();

        return $this;
    }

    /**
     * @param integer $nbOfConsumptions
     * @return Consumptions
     */
    public function setConsumptions(int $nbOfConsumptions)
    {
        $this->number_of_consumptions = $nbOfConsumptions;

        return $this;
    }

    /**
     * @param integer $nbOfConsumptions
     * @return Consumptions
     */
    public function removeConsumptions(int $nbOfConsumptions)
    {
        $this->number_of_consumptions -= $nbOfConsumptions;

        return $this;
    }

    /**
     * @param string|null $userName
     * @return Consumptions
     */
    public function setUserName($userName = null)
    {
        $this->username = $userName;

        return $this;
    }

    /**
     * @param string|null $name
     * @return Consumptions
     */
    public function setFullName($name = null)
    {
        $this->name = $name;

        return $this;
    }
}
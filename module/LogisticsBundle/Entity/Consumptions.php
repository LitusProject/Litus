<?php

namespace LogisticsBundle\Entity;

use CommonBundle\Entity\User\Person;
use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for the consumptions
 *
 * @ORM\Entity(repositoryClass="LogisticsBundle\Repository\Consumptions")
 * @ORM\Table(name="logistics_consumptions")
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
     * @return Academic
     */
    public function getAcademic()
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
     * @param Academic|null $person
     * @return Consumptions
     */
    public function setAcademic(Person $person = null)
    {
        $this->person = $person;

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
    public function addConsumptions(int $nbOfConsumptions)
    {
        $this->numberOfConsumptions += $nbOfConsumptions;

        return $this;
    }
}
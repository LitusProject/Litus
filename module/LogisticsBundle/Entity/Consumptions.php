<?php

namespace LogisticsBundle\Entity;

use CommonBundle\Entity\User\Person\Academic;
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
     * @var \CommonBundle\Entity\User\Person\Academic The academic to whom the consumptions belong
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person\Academic", cascade={"persist"})
     * @ORM\JoinColumn(name="academic", referencedColumnName="id")
     */
    private $academic;

    /**
     * @var integer the amount of consumptions for the academic
     *
     * @ORM\Column(name="numberOfConsumptions", type="integer")
     */
    private $numberOfConsumptions;

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
        return $this->academic;
    }

    /**
     * @return integer
     */
    public function getConsumptions()
    {
        return $this->numberOfConsumptions;
    }

    /**
     * @param Academic|null $academic
     * @return Consumptions
     */
    public function setAcademic(Academic $academic = null)
    {
        $this->academic = $academic;

        return $this;
    }

    /**
     * @param integer $nbOfConsumptions
     * @return Consumptions
     */
    public function setConsumptions(int $nbOfConsumptions)
    {
        $this->numberOfConsumptions = $nbOfConsumptions;

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
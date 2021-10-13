<?php

namespace DoorBundle\Entity;

use CommonBundle\Entity\User\Person\Academic;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity represents an access rule for our door.
 *
 * @ORM\Entity(repositoryClass="DoorBundle\Repository\Log")
 * @ORM\Table(name="door_log")
 */
class Log
{
    /**
     * @var integer The ID of this log entry
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var integer The ID of the academic
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person\Academic")
     * @ORM\JoinColumn(name="academic", referencedColumnName="id")
     */
    private $academic;

    /**
     * @var DateTime The timestamp of entry
     *
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

    /**
     * @param Academic $academic
     */
    public function __construct(Academic $academic)
    {
        $this->academic = $academic;
        $this->timestamp = new DateTime();
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
     * @return DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param  DateTime $timestamp
     * @return self
     */
    public function setTimestamp(DateTime $timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }
}

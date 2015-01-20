<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
namespace PromBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for a bus
 *
 * @ORM\Entity(repositoryClass="PromBundle\Repository\Bus")
 * @ORM\Table(name="prom.bus")
 */
class Bus
{
    /**
     * @var integer The ID of this article
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var DateTime The depature time of this bus
     *
     * @ORM\Column(name="departure_time", type="datetime")
     */
    private $departureTime;

    /**
     * @var int The amount seats in total.
     *
     * @ORM\Column(type="integer")
     */
    private $totalSeats;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="PromBundle\Entity\Bus\Passenger", mappedBy="bus")
     */
    private $seats;

    /**
     * Creates a new publication with the given title
     *
     * @param string $title The title of this publication
     */
    public function __construct($title)
    {
        $this->title = $title;
        $this->deleted = false;
        $this->seats = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getDepartureTime()
    {
        return $this->departureTime;
    }

    /**
     * Set the departureTime
     *
     * @param DateTime $time The departure time
     */
    public function setDepartureTime(DateTime $time)
    {
        $this->departureTime = $time;
    }

    /**
     * @return int
     */
    public function getTotalSeats()
    {
        return $this->totalSeats;
    }

    /**
     * @param $nb The total amount of seats
     */
    public function setTotalSeats($nb)
    {
        $this->totalSeats = $nb;
    }
}

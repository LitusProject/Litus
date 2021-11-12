<?php

namespace LogisticsBundle\Entity\Reservation;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use LogisticsBundle\Entity\Reservation;

/**
 * This is the entity for a resource.
 *
 * @ORM\Entity(repositoryClass="LogisticsBundle\Repository\Reservation\Resource")
 * @ORM\Table(name="logistics_reservations_resources")
 */
class Resource
{
    /**
     * @var string The name of this resource.
     *
     * @ORM\Id
     * @ORM\Column(type="string", length=30)
     */
    private $name;

    /**
     * @var ArrayCollection An array of \LogisticsBundle\Entity\Reservation indicating when this resource is reserved.
     *
     * @ORM\OneToMany(targetEntity="LogisticsBundle\Entity\Reservation", mappedBy="resource_id")
     */
    private $reservations;

    /**
     * Creates a new reservable resource.
     *
     * @param string $name The name of the resource.
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->reservations = new ArrayCollection();
    }

    /**
     * @return string The name of the resource.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array An array of \LogisticsBundle\Entity\Reservation indicating when this resource is reserved.
     */
    public function getReservations()
    {
        return $this->reservations->toArray();
    }

    /**
     * @param Reservation $reservation The reservation to add to this resource.
     */
    public function addReservation(Reservation $reservation)
    {
        $this->reservations->add($reservation);
    }
}

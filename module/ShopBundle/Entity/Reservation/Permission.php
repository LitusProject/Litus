<?php

namespace ShopBundle\Entity\Reservation;

use CommonBundle\Entity\User\Person;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores a reservation permission entry.
 *
 * @ORM\Entity(repositoryClass="ShopBundle\Repository\Reservation\Permission")
 * @ORM\Table(name="shop_reservations_permissions")
 */
class Permission
{
    /**
     * @var Person The person this permission entry belongs to
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;
    /**
     * @var boolean Whether this person is allowed to make reservations
     * @ORM\Column(type="boolean")
     */
    private $reservationsAllowed;

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @param  Person $person
     * @return self
     */
    public function setPerson($person)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getReservationsAllowed()
    {
        return $this->reservationsAllowed;
    }

    /**
     * @param  boolean $reservationsAllowed
     * @return self
     */
    public function setReservationsAllowed($reservationsAllowed)
    {
        $this->reservationsAllowed = $reservationsAllowed;

        return $this;
    }
}

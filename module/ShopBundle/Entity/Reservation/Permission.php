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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

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

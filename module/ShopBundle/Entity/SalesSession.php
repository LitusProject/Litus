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

namespace ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores a sales session.
 *
 * @author Floris Kint <floris.kint@litus.cc>
 * @ORM\Entity(repositoryClass="ShopBundle\Repository\SalesSession")
 * @ORM\Table(name="shop.sessions")
 */
class SalesSession
{
    /**
     * @var integer The ID of this sales session
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var datetime The start date of this sales session
     *
     * @ORM\Column(type="datetime")
     */
    private $startDate;

    /**
     * @var datetime The end date of this sales session
     *
     * @ORM\Column(type="datetime")
     */
    private $endDate;

    /**
     * @var boolean Whether reservations can be made for this sales session
     *
     * @ORM\Column(type="boolean")
     */
    private $reservationsPossible;

    /**
     * @param datetime $startDate
     * @param datetime $endDate
     * @param datetime $reservationsPossible
     */
    public function __construct($startDate, $endDate, $reservationsPossible)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->reservationsPossible = $reservationsPossible;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param  datetime $startDate
     * @return self
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @return datetime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param  datetime $endDate
     * @return self
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return datetime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param  boolean $reservationsPossible
     * @return self
     */
    public function setReservationsPossible($reservationsPossible)
    {
        $this->reservationsPossible = $reservationsPossible;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getReservationsPossible()
    {
        return $this->reservationsPossible;
    }
}

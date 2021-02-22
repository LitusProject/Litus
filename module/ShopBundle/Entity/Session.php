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

namespace ShopBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores a sales session.
 *
 * @ORM\Entity(repositoryClass="ShopBundle\Repository\Session")
 * @ORM\Table(name="shop_sessions")
 */
class Session
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
     * @var DateTime The start date of this sales session
     *
     * @ORM\Column(type="datetime")
     */
    private $startDate;

    /**
     * @var DateTime The end date of this sales session
     *
     * @ORM\Column(type="datetime")
     */
    private $endDate;

    /**
     * @var DateTime The end date for reservations for this sales session
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $finalReservationDate;

    /**
     * @var boolean Whether reservations can be made for this sales session
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $reservationsPossible;

    /**
     * @var string Remarks for this sales session
     *
     * @ORM\Column(type="text")
     */
    private $remarks;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    // TODO: Rename to SaleSession
    // TODO: Add __construct()

    /**
     * @param  DateTime $startDate
     * @return self
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param  DateTime $endDate
     * @return self
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param  DateTime $finalReservationDate
     * @return self
     */
    public function setFinalReservationDate($finalReservationDate)
    {
        $this->finalReservationDate = $finalReservationDate;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getFinalReservationDate()
    {
        return $this->finalReservationDate;
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

    /**
     * @param  string $remarks
     * @return self
     */
    public function setRemarks($remarks)
    {
        $this->remarks = $remarks;

        return $this;
    }

    /**
     * @return string
     */
    public function getRemarks()
    {
        return $this->remarks;
    }
}

<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
namespace LogisticsBundle\Entity\Reservation;

use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for a reservation.
 *
 * A reservation is associated with a certain resource and locks it from a given start date to a given end date.
 *
 * @ORM\Entity(repositoryClass="LogisticsBundle\Repository\Reservation\Reservation")
 * @ORM\Table(name="logistics.reservation")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="inheritance_type", type="string")
 * @ORM\DiscriminatorMap({
 *      "van"="LogisticsBundle\Entity\Reservation\VanReservation"
 * })
 */
class Reservation
{

    /**
     * @var The reservation's unique identifier
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \CommonBundle\Entity\Users\Person The creator of this reservation
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\Users\Person")
     * @ORM\JoinColumn(name="creator", referencedColumnName="id")
     */
    private $creator;

    /**
     * @var \LogisticsBundle\Entity\Reservation\ReservableResource The resource associated with this reservation.
     *
     * @ORM\ManyToOne(targetEntity="LogisticsBundle\Entity\Reservation\ReservableResource", inversedBy="reservations")
     * @ORM\JoinColumn(name="resource_name", referencedColumnName="name")
     */
    private $resource;

    /**
     * @var string The reason for this reservation
     *
     * @ORM\Column(type="text")
     */
    private $reason;

    /**
     * @var string Additional information for this reservation
     *
     * @ORM\Column(name="additional_info", type="text")
     */
    private $additionalInfo;

    /**
     * @var DateTime The start date and time of this reservation.
     *
     * @ORM\Column(name="start_date", type="datetime")
     */
    private $startDate;

    /**
     * @var DateTime The end date and time of this reservation.
     *
     * @ORM\Column(name="end_date", type="datetime")
     */
    private $endDate;

    public function __construct($startDate, $endDate, $reason, ReservableResource $resource, $additionalInfo, $creator) {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->reason = $reason;
        $this->resource = $resource;
        $this->additionalInfo = $additionalInfo;
        $this->creator = $creator;
    }

    public function getId() {
        return $this->id;
    }

    public function getResource() {
        return $this->resource;
    }

    public function getCreator() {
        return $this->creator;
    }

    public function setReason($reason) {
        $this->reason = $reason;
        return $this;
    }

    public function getReason() {
        return $this->reason;
    }

    /**
     * @param DateTime $startDate
     *
     * @return \NotificationBundle\Entity\Nodes\Notification
     */
    public function setStartDate($startDate) {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStartDate() {
        return $this->startDate;
    }

    /**
     * @param DateTime $endDate
     *
     * @return \NotificationBundle\Entity\Nodes\Notification
     */
    public function setEndDate($endDate) {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEndDate() {
        return $this->endDate;
    }

    public function setAdditionalInfo($additionalInfo) {
        $this->additionalInfo = $additionalInfo;
        return $this;
    }

    public function getAdditionalInfo() {
        return $this->additionalInfo;
    }

}
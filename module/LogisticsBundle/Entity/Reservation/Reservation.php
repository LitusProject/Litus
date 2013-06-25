<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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
 * @ORM\Table(name="logistics.reservations")
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

    /**
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param string $reason
     * @param \LogisticsBundle\Entity\Reservation\ReservableResource $resource
     * @param string $additionalInfo
     * @param \CommonBundle\Entity\Users\Person $creator
     */
    public function __construct($startDate, $endDate, $reason, ReservableResource $resource, $additionalInfo, $creator) {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->reason = $reason;
        $this->resource = $resource;
        $this->additionalInfo = $additionalInfo;
        $this->creator = $creator;
    }

    /**
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return \LogisticsBundle\Entity\Reservation\ReservableResource
     */
    public function getResource() {
        return $this->resource;
    }

    /**
     * @return \CommonBundle\Entity\Users\Person
     */
    public function getCreator() {
        return $this->creator;
    }

    /**
     * @param string $reason
     *
     * @return \LogisticsBundle\Entity\Reservation\Reservation
     */
    public function setReason($reason) {
        $this->reason = $reason;
        return $this;
    }

    /**
     * @return string
     */
    public function getReason() {
        return $this->reason;
    }

    /**
     * @param \DateTime $startDate
     *
     * @return \LogisticsBundle\Entity\Reservation\Reservation
     */
    public function setStartDate($startDate) {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate() {
        return $this->startDate;
    }

    /**
     * @param \DateTime $endDate
     *
     * @return \LogisticsBundle\Entity\Reservation\Reservation
     */
    public function setEndDate($endDate) {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate() {
        return $this->endDate;
    }

    /**
     * @param string $additionalInfo
     *
     * @return \LogisticsBundle\Entity\Reservation\Reservation
     */
    public function setAdditionalInfo($additionalInfo) {
        $this->additionalInfo = $additionalInfo;
        return $this;
    }

    /**
     * @return string
     */
    public function getAdditionalInfo() {
        return $this->additionalInfo;
    }

}

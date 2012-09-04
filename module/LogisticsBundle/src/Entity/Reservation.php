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
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
namespace LogisticsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for a reservation.
 * 
 * A reservation is associated with a certain resource and locks it from a given start date to a given end date.
 *
 * @ORM\Entity(repositoryClass="LogisticsBundle\Repository\Reservation")
 * @ORM\Table(name="logistics.reservations")
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
     * @var \LogisticsBundle\Entity\ReservableResource The resource associated with this reservation.
     * 
     * @ORM\ManyToOne(targetEntity="LogisticsBundle\Entity\ReservableResource", inversedBy="reservations")
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
    
    public function __construct($startDate, $endDate, $reason, ReservableResource $resource, $additionalInfo = '') {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->reason = $reason;
        $this->resource = $resource;
        $this->additionalInfo = $additionalInfo;
    }
    
    public function getResourcd() {
        return $resource;
    }
    
    public function setReason($reason) {
        $this->reason = $reason;
        return $this;
    }
    
    public function getReason() {
        return $reason;
    }
    
    public function setStartDate($startDate) {
        $this->startDate = $startDate;
        return $this;
    }
    
    public function getStartDate() {
        return $startDate;
    }
    
    public function setEndDate($endDate) {
        $this->endDate = $endDate;
        return $this;
    }
    
    public function getEndDate() {
        return $endDate;
    }
    
    public function setAdditionalInfo($additionalInfo) {
        $this->additionalInfo = $additionalInfo;
        return $this;
    }
    
    public function getAdditionalInfo($additionalInfo) {
        return $this->additionalInfo;
    }
    
}
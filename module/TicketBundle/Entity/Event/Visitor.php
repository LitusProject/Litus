<?php

namespace TicketBundle\Entity\Event;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use TicketBundle\Entity\Event;

/**
 * Visitor
 *
 * The log of a qr code entering the premise and keeping track whether they left
 *
 * @ORM\Entity(repositoryClass="TicketBundle\Repository\Event\Visitor")
 * @ORM\Table(name="ticket_events_visitors")
 */
class Visitor
{
    /**
     * @var integer The ID of the Visitor
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Event The event to which the Qr code belongs
     *
     * @ORM\ManyToOne(targetEntity="TicketBundle\Entity\Event")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $event;

    /**
     * @var string QR code of the visitor
     *
     * @ORM\Column(name="qr_code", type="text")
     */
    private $qrCode;

    /**
     * @var DateTime The entry time
     *
     * @ORM\Column(name="entry_time", type="datetime", nullable=true)
     */
    private $entryTime;

    /**
     * @var DateTime The exit time
     *
     * @ORM\Column(name="exit_time", type="datetime", nullable=true)
     */
    private $exitTimestamp;

    /**
     * @param Event $event
     * @param string $qrCode
     */
    public function __construct($event, $qrCode)
    {
        $this->event = $event;
        $this->qrCode = $qrCode;
        $this->entryTime = new DateTime();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return string
     */
    public function getQrCode()
    {
        return $this->qrCode;
    }

    /**
     * @return DateTime
     */
    public function getEntryTime()
    {
        return $this->entryTime;
    }

    /**
     * @param DateTime $exitTimestamp
     * @return self
     */
    public function setExitTimestamp($exitTimestamp)
    {
        $this->exitTimestamp = $exitTimestamp;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getExitTimestamp()
    {
        return $this->exitTimestamp;
    }
}

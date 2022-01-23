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

namespace BrBundle\Entity\Event;

use BrBundle\Entity\Event;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Visitor
 * 
 * The log of a qr code entering the premise and keeping track whether they left
 * Using the qr code means the data stays anonimized even after all subscribers are removed
 *
 * @author Belian Callaerts <belian.callaerts@vtk.be>
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Event\Visitor")
 * @ORM\Table(name="br_events_visitors")
 */
class Visitor
{
    /**
     * @var integer The ID of the location
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     *@var Event The event that the company will be attending
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Event")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $event;

    /**
     * @var string QR code of the visitor
     *
     * @ORM\Column(name="qr_code", type="text")
     *
     */
    private $qrCode;


    /**
     * @var DateTime The start date and time of this event.
     *
     * @ORM\Column(name="entry_timestamp", type="datetime")
     */
    private $entryTimestamp;

    /**
     * @var DateTime The start date and time of this event.
     *
     * @ORM\Column(name="exit_timestamp", type="datetime", nullable=true)
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
        $this->entryTimestamp = new DateTime();
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
    public function getEntryTimestamp()
    {
        return $this->entryTimestamp;
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
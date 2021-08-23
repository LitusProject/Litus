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

namespace TicketBundle\Entity;

use CommonBundle\Entity\User\Person,
    DateTime,
    Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="TicketBundle\Repository\OrderEntity")
 * @ORM\Table(name="tickets.orders")
 */
class OrderEntity
{

    /**
     * @var integer The ID of the ticket
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Event The event of the ticket
     *
     * @ORM\ManyToOne(targetEntity="TicketBundle\Entity\Event", inversedBy="orders")
     * @ORM\JoinColumn(name="event", referencedColumnName="id")
     */
    private $event;

    /**
     * @var Person|null The person who bought/reserved the order
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="booker", referencedColumnName="id")
     */
    private $booker;

    /**
     * @var DateTime|null The date the ticket was booked
     *
     * @ORM\Column(name="book_date", type="datetime", nullable=true)
     */
    private $bookDate;

    /**
     * @var ArrayCollection The tickets of this order
     *
     * @ORM\OneToMany(targetEntity="TicketBundle\Entity\Ticket", mappedBy="orderEntity", cascade={"remove"})
     */
    private $tickets;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param Event $event
     */
    public function setEvent($event)
    {
        $this->event = $event;
    }

    /**
     * @return Person|null
     */
    public function getBooker()
    {
        return $this->booker;
    }

    /**
     * @param Person|null $booker
     */
    public function setBooker($booker)
    {
        $this->booker = $booker;
    }

    /**
     * @return DateTime|null
     */
    public function getBookDate()
    {
        return $this->bookDate;
    }

    /**
     * @param DateTime|null $bookDate
     */
    public function setBookDate($bookDate)
    {
        $this->bookDate = $bookDate;
    }

    /**
     * @return ArrayCollection
     */
    public function getTickets()
    {
        return $this->tickets;
    }

    /**
     * @param Ticket $tickets
     */
    public function setTickets($tickets)
    {
        $this->tickets = $tickets;
    }

    /**
     * @param Ticket $ticket
     */
    public function addTicket($ticket)
    {
        $this->tickets[] = $ticket;
    }



}

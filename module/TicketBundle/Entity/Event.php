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

namespace TicketBundle\Entity;

use CalendarBundle\Entity\Nodes\Event as CalendarEvent,
    DateTime,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="TicketBundle\Repository\Event")
 * @ORM\Table(name="tickets.events")
 */
class Event
{
    /**
     * @var integer The ID of the event
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The activity of the event
     *
     * @ORM\OneToOne(targetEntity="CalendarBundle\Entity\Nodes\Event")
     * @ORM\JoinColumn(name="activity", referencedColumnName="id")
     */
    private $activity;

    /**
     * @var boolean Flag whether the tickets are bookable
     *
     * @ORM\Column(type="boolean")
     */
    private $bookable;

    /**
     * @var \DateTime The date the booking system will close
     *
     * @ORM\Column(name="bookings_close_date", type="datetime", nullable=true)
     */
    private $bookingsCloseDate;

    /**
     * @var boolean Flag whether the event booking system is active
     *
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @var boolean Flag whether the tickets are generated
     *
     * @ORM\Column(name="tickets_generated", type="boolean")
     */
    private $ticketsGenerated;

    /**
     * @var integer The total number of tickets
     *
     * @ORM\Column(name="number_of_tickets", type="integer", nullable=true)
     */
    private $numberOfTickets;

    /**
     * @var integer The maximum number of tickets bookable by one person
     *
     * @ORM\Column(name="limit_per_person", type="integer", nullable=true)
     */
    private $limitPerPerson;

    /**
     * @var integer Flag whether only members can book tickets
     *
     * @ORM\Column(name="only_members", type="boolean")
     */
    private $onlyMembers;

    /**
     * @var integer The price for members
     *
     * @ORM\Column(name="price_members", type="smallint")
     */
    private $priceMembers;

    /**
     * @var integer The price for non members
     *
     * @ORM\Column(name="price_non_members", type="smallint")
     */
    private $priceNonMembers;

    /**
     * @var \Doctrine\Common\Collection\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="TicketBundle\Entity\Ticket", mappedBy="event")
     */
    private $tickets;

    /**
     * @param \CalendarBundle\Entity\Nodes\Event $activity
     * @param boolean $bookable
     * @param \DateTime $bookingsCloseDate
     * @param boolean $active
     * @param boolean $ticketsGenerated
     * @param integer $numberOfTickets
     * @param integer $limitPerPerson
     * @param boolean $onlyMembers
     * @param integer $priceMembers
     * @param integer $priceNonMembers
     */
    public function __construct(CalendarEvent $activity, $bookable, DateTime $bookingsCloseDate = null, $active, $ticketsGenerated, $numberOfTickets = null, $limitPerPerson = null, $onlyMembers, $priceMembers, $priceNonMembers)
    {
        $this->activity = $activity;
        $this->bookable = $bookable;
        $this->bookingsCloseDate = $bookingsCloseDate;
        $this->active = $active;
        $this->ticketsGenerated = $ticketsGenerated;
        $this->numberOfTickets = $numberOfTickets;
        $this->limitPerPerson = $limitPerPerson;
        $this->onlyMembers = $onlyMembers;

        $this->setPriceMembers($priceMembers)
            ->setPriceNonMembers($priceNonMembers);
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \CalendarBundle\Entity\Nodes\Event
     */
    public function getActivity()
    {
        return $this->activity;
    }

    /**
     * @param \CalendarBundle\Entity\Nodes\Event $activity
     * @return \TicketBunlde\Entity\Event
     */
    public function setActivity(CalendarEvent $activity)
    {
        $this->activity = $activity;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isBookable()
    {
        return $this->bookable;
    }

    /**
     * @param boolean $bookable
     * @return \TicketBunlde\Entity\Event
     */
    public function setBookable($bookable)
    {
        $this->bookable = $bookable;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getBookingsCloseDate()
    {
        return $this->bookingsCloseDate;
    }

    /**
     * @param \DateTime $bookingsCloseDate
     * @return \TicketBunlde\Entity\Event
     */
    public function setBookingsCloseDate(DateTime $bookingsCloseDate)
    {
        $this->bookingsCloseDate = $bookingsCloseDate;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     * @return \TicketBunlde\Entity\Event
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return boolean
     */
    public function areTicketsGenerated()
    {
        return $this->ticketsGenerated;
    }

    /**
     * @param boolean $ticketsGenerated
     * @return \TicketBunlde\Entity\Event
     */
    public function setTicketsGenerated($ticketsGenerated)
    {
        $this->ticketsGenerated = $ticketsGenerated;
        return $this;
    }

    /**
     * @return integer
     */
    public function getNumberOfTickets()
    {
        return $this->numberOfTickets;
    }

    /**
     * @param integer $numberOfTickets
     * @return \TicketBunlde\Entity\Event
     */
    public function setNumberOfTickets($numberOfTickets)
    {
        $this->numberOfTickets = $numberOfTickets;
        return $this;
    }

    /**
     * @return integer
     */
    public function getLimitPerPerson()
    {
        return $this->limitPerPerson;
    }

    /**
     * @param integer $limitPerPerson
     * @return \TicketBunlde\Entity\Event
     */
    public function setLimitPerPerson($limitPerPerson)
    {
        $this->limitPerPerson = $limitPerPerson;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isOnlyMembers()
    {
        return $this->onlyMembers;
    }

    /**
     * @param boolean $onlyMembers
     * @return \TicketBunlde\Entity\Event
     */
    public function setOnlyMembers($onlyMembers)
    {
        $this->onlyMembers = $onlyMembers;
        return $this;
    }

    /**
     * @return integer
     */
    public function getPriceMembers()
    {
        return $this->priceMembers;
    }

    /**
     * @param integer $priceMembers
     * @return \TicketBunlde\Entity\Event
     */
    public function setPriceMembers($priceMembers)
    {
        $this->priceMembers = $priceMembers * 100;
        return $this;
    }

    /**
     * @return integer
     */
    public function getPriceNonMembers()
    {
        return $this->priceNonMembers;
    }

    /**
     * @param integer $priceNonMembers
     * @return \TicketBunlde\Entity\Event
     */
    public function setPriceNonMembers($priceNonMembers)
    {
        $this->priceNonMembers = $priceNonMembers * 100;
        return $this;
    }

    /**
     * @return \Doctrine\Common\Collection\ArrayCollection
     */
    public function getTickets()
    {
        return $this->tickets;
    }

    /**
     * @return integer
     */
    public function getNumberSold()
    {
        $sold = 0;
        foreach($this->tickets as $ticket) {
            if ($ticket->getStatusCode() == 'sold')
                $sold++;
        }
        return $sold;
    }

    public function getNumberFree()
    {
        return $this->getNumberOfTickets() - $this->getNumberSold();
    }

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @return integer
     */
    public function generateTicketNumber(EntityManager $entityManager)
    {
        do {
            $number = rand();
            $ticket = $entityManager
                ->getRepository('TicketBundle\Entity\Ticket')
                ->findOneByEventAndNumber($this, $number);
        } while($ticket !== null);

        return $number;
    }
}
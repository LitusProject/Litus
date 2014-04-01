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

namespace TicketBundle\Entity;

use CalendarBundle\Entity\Node\Event as CalendarEvent,
    CommonBundle\Entity\User\Person,
    DateInterval,
    DateTime,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM,
    \Doctrine\Common\Collection\ArrayCollection;

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
     * @var CalendarEvent The activity of the event
     *
     * @ORM\OneToOne(targetEntity="CalendarBundle\Entity\Node\Event")
     * @ORM\JoinColumn(name="activity", referencedColumnName="id")
     */
    private $activity;

    /**
     * @var boolean Flag whether the tickets are bookable for praesidium
     *
     * @ORM\Column(name="bookable_praesidium", type="boolean")
     */
    private $bookablePraesidium;

    /**
     * @var boolean Flag whether the tickets are bookable
     *
     * @ORM\Column(type="boolean")
     */
    private $bookable;

    /**
     * @var DateTime|null The date the booking system will close
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
     * @var integer Flag whether users can remove there ticket
     *
     * @ORM\Column(name="allow_remove", type="boolean")
     */
    private $allowRemove;

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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="TicketBundle\Entity\Option", mappedBy="event")
     */
    private $options;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="TicketBundle\Entity\Ticket", mappedBy="event")
     */
    private $tickets;

    /**
     * @param CalendarEvent $activity
     * @param boolean       $bookablePraesidium
     * @param boolean       $bookable
     * @param DateTime      $bookingsCloseDate
     * @param boolean       $active
     * @param boolean       $ticketsGenerated
     * @param integer       $numberOfTickets
     * @param integer       $limitPerPerson
     * @param boolean       $allowRemove
     * @param boolean       $onlyMembers
     * @param integer       $priceMembers
     * @param integer       $priceNonMembers
     */
    public function __construct(CalendarEvent $activity, $bookablePraesidium, $bookable, DateTime $bookingsCloseDate = null, $active, $ticketsGenerated, $numberOfTickets = null, $limitPerPerson = null, $allowRemove, $onlyMembers, $priceMembers, $priceNonMembers)
    {
        $this->activity = $activity;
        $this->bookablePraesidium = $bookablePraesidium;
        $this->bookable = $bookable;
        $this->bookingsCloseDate = $bookingsCloseDate;
        $this->active = $active;
        $this->ticketsGenerated = $ticketsGenerated;
        $this->numberOfTickets = $numberOfTickets;
        $this->limitPerPerson = $limitPerPerson;
        $this->onlyMembers = $onlyMembers;
        $this->allowRemove = $allowRemove;

        $this->options = new ArrayCollection();
        $this->tickets = new ArrayCollection();

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
     * @return CalendarEvent
     */
    public function getActivity()
    {
        return $this->activity;
    }

    /**
     * @param  CalendarEvent $activity
     * @return self
     */
    public function setActivity(CalendarEvent $activity)
    {
        $this->activity = $activity;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isBookablePraesidium()
    {
        return $this->bookablePraesidium;
    }

    /**
     * @return boolean
     */
    public function isStillBookablePraesidium()
    {
        return $this->bookablePraesidium && (new DateTime() < $this->getBookingsCloseDate() || $this->getBookingsCloseDate() === null);
    }

    /**
     * @param  boolean $bookablePraesidium
     * @return self
     */
    public function setBookablePraesidium($bookablePraesidium)
    {
        $this->bookablePraesidium = $bookablePraesidium;

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
     * @return boolean
     */
    public function isStillBookable()
    {
        return $this->bookable && (new DateTime() < $this->getBookingsCloseDate() || $this->getBookingsCloseDate() === null);
    }

    /**
     * @param  boolean $bookable
     * @return self
     */
    public function setBookable($bookable)
    {
        $this->bookable = $bookable;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getBookingsCloseDate()
    {
        return $this->bookingsCloseDate;
    }

    /**
     * @param  DateTime|null $bookingsCloseDate
     * @return self
     */
    public function setBookingsCloseDate(DateTime $bookingsCloseDate = null)
    {
        $this->bookingsCloseDate = $bookingsCloseDate;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        if ($this->activity->getStartDate() < new DateTime())
            return false;

        return $this->active;
    }

    /**
     * @param  boolean $active
     * @return self
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
     * @param  boolean $ticketsGenerated
     * @return self
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
     * @param  integer $numberOfTickets
     * @return self
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
     * @param  integer $limitPerPerson
     * @return self
     */
    public function setLimitPerPerson($limitPerPerson)
    {
        $this->limitPerPerson = $limitPerPerson;

        return $this;
    }

    /**
     * @return boolean
     */
    public function allowRemove()
    {
        return $this->allowRemove;
    }

    /**
     * @param  boolean $allowRemove
     * @return self
     */
    public function setAllowRemove($allowRemove)
    {
        $this->allowRemove = $allowRemove;

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
     * @param  boolean $onlyMembers
     * @return self
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
     * @param  integer $priceMembers
     * @return self
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
     * @param  integer $priceNonMembers
     * @return self
     */
    public function setPriceNonMembers($priceNonMembers)
    {
        $this->priceNonMembers = $priceNonMembers * 100;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getTickets()
    {
        return $this->tickets;
    }

    /**
     * @return ArrayCollection
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return integer
     */
    public function getNumberSold()
    {
        $sold = 0;
        foreach ($this->tickets as $ticket) {
            if ($ticket->getStatusCode() == 'sold')
                $sold++;
        }

        return $sold;
    }

    /**
     * @return integer
     */
    public function getNumberBooked()
    {
        $sold = 0;
        foreach ($this->tickets as $ticket) {
            if ($ticket->getStatusCode() == 'booked')
                $sold++;
        }

        return $sold;
    }

    public function getNumberFree()
    {
        return $this->getNumberOfTickets() - $this->getNumberSold() - $this->getNumberBooked();
    }

    /**
     * @param  EntityManager $entityManager
     * @return integer
     */
    public function generateTicketNumber(EntityManager $entityManager)
    {
        do {
            $number = rand();
            $ticket = $entityManager
                ->getRepository('TicketBundle\Entity\Ticket')
                ->findOneByEventAndNumber($this, $number);
        } while ($ticket !== null);

        return $number;
    }

    /**
     * Check whether or not the given person can sign out from this shift.
     *
     * @param  EntityManager $entityManager The EntityManager instance
     * @param  Person        $person        The person that should be checked
     * @return boolean
     */
    public function canRemoveReservation(EntityManager $entityManager, Person $person)
    {
        if (!$this->allowRemove())
            return false;

        $now = new DateTime();

        $removeReservationThreshold = new DateInterval(
            $entityManager->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('ticket.remove_reservation_treshold')
        );

        if ($this->getBookingsCloseDate() == null) {
            $getStartDate = clone $this->getActivity()->getStartDate();
        } else {
            $getStartDate = clone $this->getBookingsCloseDate();
        }

        if ($getStartDate->sub($removeReservationThreshold) < $now)
             return false;

        return true;
    }

    /**
     * @param  Option|null $option
     * @param  boolean     $member
     * @return integer
     */
    public function getNumberSoldByOption(Option $option = null, $member)
    {
        $number = 0;
        foreach ($this->tickets as $ticket) {
            if ($ticket->getStatusCode() !== 'sold')
                continue;

            if (null !== $option) {
                if (($ticket->getOption() == $option) && (($ticket->isMember() && $member) || (!$ticket->isMember() && !$member)))
                    $number++;
            } else {
                if (($ticket->isMember() && $member) || (!$ticket->isMember() && !$member))
                    $number++;
            }
        }

        return $number;
    }

    /**
     * @param  Option|null $option
     * @param  boolean     $member
     * @return integer
     */
    public function getNumberBookedByOption(Option $option = null, $member)
    {
        $number = 0;
        foreach ($this->tickets as $ticket) {
            if ($ticket->getStatusCode() !== 'booked')
                continue;

            if (null !== $option) {
                if (($ticket->getOption() == $option) && (($ticket->isMember() && $member) || (!$ticket->isMember() && !$member)))
                    $number++;
            } else {
                if (($ticket->isMember() && $member) || (!$ticket->isMember() && !$member))
                    $number++;
            }
        }

        return $number;
    }
}

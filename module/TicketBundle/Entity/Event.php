<?php

namespace TicketBundle\Entity;

use CalendarBundle\Entity\Node\Event as CalendarEvent;
use DateInterval;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use FormBundle\Entity\Node\Form;
use TicketBundle\Entity\Event\Option;

/**
 * @ORM\Entity(repositoryClass="TicketBundle\Repository\Event")
 * @ORM\Table(name="ticket_events")
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
     * @var string The random ID of the event
     *
     * @ORM\Column(type="string", unique=true, nullable=true)
     */
    private $rand_id;

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
     * @var boolean Flag whether the event booking system is visible in the calendar
     *
     * @ORM\Column(type="boolean", options={"default" : false})
     */
    private $visible;

    /**
     * @var boolean Flag whether the tickets are generated
     *
     * @ORM\Column(name="tickets_generated", type="boolean", nullable=true)
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
     * @var boolean Flag whether only members can book tickets
     *
     * @ORM\Column(name="only_members", type="boolean")
     */
    private $onlyMembers;

    /**
     * @var boolean Flag whether users can remove there ticket
     *
     * @ORM\Column(name="allow_remove", type="boolean")
     */
    private $allowRemove;

    /**
     * @var integer The price for members
     *
     * @ORM\Column(name="price_members", type="integer", nullable=true)
     */
    private $priceMembers;

    /**
     * @var integer The price for non members
     *
     * @ORM\Column(name="price_non_members", type="integer", nullable=true)
     */
    private $priceNonMembers;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="TicketBundle\Entity\Event\Option", mappedBy="event")
     */
    private $options;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="TicketBundle\Entity\Ticket", mappedBy="event")
     */
    private $tickets;

    /**
     * @var string the base string for Invoice Id's
     *
     * @ORM\Column(name="invoice_id_base", type="string", length=32, nullable=true)
     */
    private $invoiceIdBase;

    /**
     * @var string the base string for Order Id's
     *
     * @ORM\Column(name="order_id_base", type="string", length=7, nullable=true)
     */
    private $orderIdBase;

    /**
     * @var string The next invoice Id number.
     *
     * @ORM\Column(name="next_invoice_nb", type="string", length=4, options={"default" : "0000"})
     */
    private $nextInvoiceNb;

    /**
     * @var boolean Flag whether users can pay for their ticket online
     *
     * @ORM\Column(name="online_payment", type="boolean", options={"default" : false})
     */
    private $onlinePayment;

    /**
     * @var string The text for this event
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @var Form The form for the event
     *
     * @ORM\OneToOne(targetEntity="FormBundle\Entity\Node\Form")
     * @ORM\JoinColumn(name="form", referencedColumnName="id", nullable=true)
     */
    private $form;

    /**
     * @var boolean Whether or not qr codes are enabled
     *
     * @ORM\Column(name="qr_enabled", type="boolean", options={"default" : false})
     */
    private $qrEnabled;

    /**
     * @var string The email address the mails are sent from
     *
     * @ORM\Column(name="mail_from", type="string", nullable=true)
     */
    private $mailFrom;

    /**
     * @var string The subject for the confirmation mail
     *
     * @ORM\Column(name="mail_confirmation_subject", type="string", nullable=true)
     */
    private $confirmationMailSubject;

    /**
     * @var string The body for the confirmation mail
     *
     * @ORM\Column(name="mail_confirmation_body", type="text", nullable=true)
     */
    private $confirmationMailBody;

    /**
     * @var boolean whether or not the pay page should be accessible after 24 hours
     *
     * @ORM\Column(name="deadline_enabled", type="boolean", nullable=true)
     */
    private $payDeadline;

    /**
     * @var integer The amount of time before a ticket is invalid
     *
     * @ORM\Column(name="deadline_time", type="bigint", nullable=true)
     */
    private $deadlineTime;

    /**
     * @var string The link to the terms and conditions
     *
     * @ORM\Column(name="terms_url", type="string", nullable=true)
     */
    private $termsUrl;

    public function __construct(string $rand_id)
    {
        $this->rand_id = $rand_id;
        $this->options = new ArrayCollection();
        $this->tickets = new ArrayCollection();
        $this->nextInvoiceNb = '0000';
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getRandId()
    {
        if (is_null($this->rand_id))
            return $this->id;
        return $this->rand_id;
    }

    /**
     * @return CalendarEvent
     */
    public function getActivity()
    {
        return $this->activity;
    }

    /**
     * @param  CalendarEvent|null $activity
     * @return self
     */
    public function setActivity($activity)
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
        if ($this->activity->getEndDate() < new DateTime()) {
            return false;
        }

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
    public function isVisible()
    {
        if ($this->activity->getEndDate() < new DateTime()) {
            return false;
        }

        return $this->visible;
    }

    /**
     * @param  boolean $visible
     * @return self
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

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
     * @param  integer|null $priceNonMembers
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
     * @return string|null
     */
    public function getInvoiceIdBase()
    {
        return $this->invoiceIdBase;
    }

    /**
     * @param string $invoiceIdBase
     * @return self
     */
    public function setInvoiceIdBase(string $invoiceIdBase)
    {
        $this->invoiceIdBase = $invoiceIdBase;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getOrderIdBase()
    {
        return $this->orderIdBase;
    }

    /**
     * @param string $orderIdBase
     * @return self
     */
    public function setOrderIdBase(string $orderIdBase)
    {
        $this->orderIdBase = $orderIdBase;
        return $this;
    }

    /**
     * @param EntityManager $em
     * @return string|null
     */
    public function findNextInvoiceNb(EntityManager $em)
    {
        $ticketInvoiceIds = $em->getRepository('TicketBundle\Entity\Ticket')->findAllInvoiceIdsByEvent($this);
        $ticketIds = array();
        foreach ($ticketInvoiceIds as $id) {
            array_push($ticketIds, substr($id['invoiceId'], -4));
        }
        for ($i = 0; $i <= 9999; $i++) {
            if (!in_array($i, $ticketIds)) {
                $this->nextInvoiceNb = str_pad(strval($i), 4, '0', STR_PAD_LEFT);
                return $this->nextInvoiceNb;
            }
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getNextInvoiceNb()
    {
        return $this->nextInvoiceNb;
    }

    /**
     * @param integer $nextInvoiceNb
     */
    public function setNextInvoiceNb(int $nextInvoiceNb)
    {
        $this->nextInvoiceNb = $nextInvoiceNb;
    }

    /**
     * @return boolean
     */
    public function isOnlinePayment()
    {
        return $this->onlinePayment;
    }

    /**
     * @param boolean $onlinePayment
     * @return self
     */
    public function setOnlinePayment($onlinePayment)
    {
        $this->onlinePayment = $onlinePayment;

        return $this;
    }

    /**
     * @return integer
     */
    public function getNumberSold()
    {
        $sold = 0;
        foreach ($this->tickets as $ticket) {
            if ($ticket->getStatusCode() == 'sold') {
                $sold++;
            }
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
            if ($ticket->getStatusCode() == 'booked') {
                $sold++;
            }
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
     * @return boolean
     */
    public function canRemoveReservation(EntityManager $entityManager)
    {
        if (!$this->allowRemove()) {
            return false;
        }

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

        return !($getStartDate->sub($removeReservationThreshold) < $now);
    }

    /**
     * @param  Option|null $option
     * @param  boolean     $member
     * @return integer
     */
    public function getNumberSoldByOption(Option $option = null, $member = false)
    {
        $number = 0;
        foreach ($this->tickets as $ticket) {
            if ($ticket->getStatusCode() !== 'sold') {
                continue;
            }

            if ($option !== null) {
                if (($ticket->getOption() == $option) && (($ticket->isMember() && $member) || (!$ticket->isMember() && !$member))) {
                    $number++;
                }
            } else {
                if (($ticket->isMember() && $member) || (!$ticket->isMember() && !$member)) {
                    $number++;
                }
            }
        }

        return $number;
    }

    /**
     * @param  Option|null $option
     * @param  boolean     $member
     * @return integer
     */
    public function getNumberBookedByOption(Option $option = null, $member = false)
    {
        $number = 0;
        foreach ($this->tickets as $ticket) {
            if ($ticket->getStatusCode() !== 'booked') {
                continue;
            }

            if ($option !== null) {
                if (($ticket->getOption() == $option) && (($ticket->isMember() && $member) || (!$ticket->isMember() && !$member))) {
                    $number++;
                }
            } else {
                if (($ticket->isMember() && $member) || (!$ticket->isMember() && !$member)) {
                    $number++;
                }
            }
        }

        return $number;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description == null ? '' : $this->description;
    }

    /**
     * @param  string $description The description
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param Form $form the form
     * @return self
     */
    public function setForm($form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getQrEnabled()
    {
        return $this->qrEnabled;
    }

    /**
     * @param boolean $qr
     * @return self
     */
    public function setQrEnabled($qr)
    {
        $this->qrEnabled = $qr;

        return $this;
    }

    /**
     * @return string
     */
    public function getMailFrom()
    {
        return $this->mailFrom;
    }

    /**
     * @param string $mail
     * @return self
     */
    public function setMailFrom($mail)
    {
        $this->mailFrom = $mail;
        return $this;
    }

    /**
     * @return string
     */
    public function getConfirmationMailSubject()
    {
        return $this->confirmationMailSubject;
    }

    /**
     * @param string $subject
     * @return self
     */
    public function setConfirmationMailSubject(string $subject)
    {
        $this->confirmationMailSubject = $subject;
        return $this;
    }

    /**
     * @return string
     */
    public function getConfirmationMailBody()
    {
        return $this->confirmationMailBody;
    }

    /**
     * @param string $body
     * @return self
     */
    public function setConfirmationMailBody(string $body)
    {
        $this->confirmationMailBody = $body;
        return $this;
    }

    /**
     * @return bool
     */
    public function getPayDeadline()
    {
        return $this->payDeadline;
    }

    /**
     * @param boolean $deadline
     * @return self
     */
    public function setPayDeadline($deadline)
    {
        $this->payDeadline = $deadline;
        return $this;
    }

    /**
     * @return int
     */
    public function getDeadlineTime()
    {
        return $this->deadlineTime;
    }

    /**
     * @param integer|null $time
     * @return self
     */
    public function setDeadlineTime($time)
    {
        $this->deadlineTime = $time;
        return $this;
    }

    /**
     * @return string
     */
    public function getTermsUrl()
    {
        return $this->termsUrl;
    }

    /**
     * @param string|null $url
     * @return self
     */
    public function setTermsUrl($url)
    {
        $this->termsUrl = $url;
        return $this;
    }
}

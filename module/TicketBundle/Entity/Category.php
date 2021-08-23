<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 10/3/18
 * Time: 1:26 PM
 */


namespace TicketBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection,
    DateTime,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="TicketBundle\Repository\Category")
 * @ORM\Table(name="tickets.events_categories")
 */
class Category {

    /**
     * @var integer The ID of the category
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The category
     *
     * @ORM\Column(type="string")
     */
    private $category;

    /**
     * @var Event The event this category is connected to.
     *
     * @ORM\ManyToOne(targetEntity="TicketBundle\Entity\Event", inversedBy="bookingCategories")
     * @ORM\JoinColumn(name="event", referencedColumnName="id")
     */
    private $event;

    /**
     * @var DateTime|null The opening date for this category.
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $bookingOpenDate;

    /**
     * @var DateTime|null The closing date for this category.
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $bookingCloseDate;

    /**
     * @var integer The maximum amount of tickets for this category.
     *
     * @ORM\Column(type="integer")
     */
    private $maxNumberTickets;

    /**
     * @var integer The maximum amount of guests this category can bring.
     *
     * @ORM\Column(type="integer")
     */
    private $maxAmountGuests;

    /**
     * @var ArrayCollection The options for this category.
     *
     * @ORM\OneToMany(targetEntity="TicketBundle\Entity\Option", mappedBy="category", cascade={"remove"})
     */
    private $options;

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
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param string $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
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
     * @return DateTime|null
     */
    public function getBookingOpenDate()
    {
        return $this->bookingOpenDate;
    }

    /**
     * @param DateTime|null $bookingOpenDate
     */
    public function setBookingOpenDate($bookingOpenDate)
    {
        $this->bookingOpenDate = $bookingOpenDate;
    }

    /**
     * @return DateTime|null
     */
    public function getBookingCloseDate()
    {
        return $this->bookingCloseDate;
    }

    /**
     * @param DateTime|null $bookingCloseDate
     */
    public function setBookingCloseDate($bookingCloseDate)
    {
        $this->bookingCloseDate = $bookingCloseDate;
    }

    /**
     * @return int
     */
    public function getMaxNumberTickets()
    {
        return $this->maxNumberTickets;
    }

    /**
     * @param int $maxNumberTickets
     */
    public function setMaxNumberTickets($maxNumberTickets)
    {
        $this->maxNumberTickets = $maxNumberTickets;
    }

    /**
     * @return int
     */
    public function getMaxAmountGuests()
    {
        return $this->maxAmountGuests;
    }

    /**
     * @param int $maxAmountGuests
     */
    public function setMaxAmountGuests($maxAmountGuests)
    {
        $this->maxAmountGuests = $maxAmountGuests;
    }

    /**
     * @return ArrayCollection
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param ArrayCollection $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    public function addOption($option) {
        $this->options[] = $option;
    }


}
<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 10/3/18
 * Time: 12:50 PM
 */

namespace TicketBundle\Entity;


use CalendarBundle\Entity\Node\Event as CalendarEvent,
    DateInterval,
    DateTime,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="TicketBundle\Repository\Event")
 * @ORM\Table(name="tickets.events")
 */
class Event {

    /**
     * @var integer The ID of the event
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var boolean Flag whether the event booking system is active
     *
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @var CalendarEvent The activity of the event
     *
     * @ORM\OneToOne(targetEntity="CalendarBundle\Entity\Node\Event")
     * @ORM\JoinColumn(name="activity", referencedColumnName="id")
     */
    private $activity;

    /**
     * @var ArrayCollection The categories that can book tickets
     *
     * @ORM\OneToMany(targetEntity="TicketBundle\Entity\Category", mappedby="event")
     */
    private $bookingCategories;

    /**
     * @var boolean Flag whether the reservation can be removed by the user.
     *
     * @ORM\Column(type="boolean")
     */
    private $allowRemove;

    /**
     * @var integer The maximum number of tickets available
     *
     * @ORM\Column(type="integer", name="max_number_tickets")
     */
    private $maxNumberTickets;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="TicketBundle\Entity\Option", mappedBy="event")
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
     * @return Event
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     * @return Event
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return CalendarEvent
     */
    public function getActivity()
    {
        return $this->activity;
    }

    /**
     * @param CalendarEvent $activity
     * @return Event
     */
    public function setActivity($activity)
    {
        $this->activity = $activity;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getBookingCategories()
    {
        return $this->bookingCategories;
    }

    /**
     * @param ArrayCollection $bookingCategories
     * @return Event
     */
    public function setBookingCategories($bookingCategories)
    {
        $this->bookingCategories = $bookingCategories;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAllowRemove()
    {
        return $this->allowRemove;
    }

    /**
     * @param bool $allowRemove
     * @return Event
     */
    public function setAllowRemove($allowRemove)
    {
        $this->allowRemove = $allowRemove;
        return $this;
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
     * @return Event
     */
    public function setMaxNumberTickets($maxNumberTickets)
    {
        $this->maxNumberTickets = $maxNumberTickets;
        return $this;
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
     * @return Event
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

   



}
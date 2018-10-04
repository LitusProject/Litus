<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 10/3/18
 * Time: 12:50 PM
 */

namespace TicketBundle\Entity;


use CalendarBundle\Entity\Node\Event as CalendarEvent,
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
     * @ORM\OneToMany(targetEntity="TicketBundle\Entity\Category", mappedBy="event")
     * @ORM\JoinColumn(name="booking_category", referencedColumnName="id")
     */
    private $bookingCategories;

    /**
     * @var boolean Flag whether the reservation can be removed by the user.
     *
     * @ORM\Column(type="boolean")
     */
    private $allowRemove;

    /**
     * @var integer The maximum number of tickets available. If negative, look inside the
     * bookingCategories. Zero means an infinite amount.
     *
     * @ORM\Column(type="integer")
     */
    private $maxNumberTickets;

    /**
     * @var ArrayCollection The orders for this event.
     *
     * @ORM\OneToMany(targetEntity="TicketBundle\Entity\Order", mappedBy="event")
     */
    private $orders;

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
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive($active)
    {
        $this->active = $active;
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
     */
    public function setActivity($activity)
    {
        $this->activity = $activity;
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
     */
    public function setBookingCategories($bookingCategories)
    {
        $this->bookingCategories = $bookingCategories;
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
     */
    public function setAllowRemove($allowRemove)
    {
        $this->allowRemove = $allowRemove;
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
     * @return ArrayCollection
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @param ArrayCollection $orders
     */
    public function setOrders($orders)
    {
        $this->orders = $orders;
    }


}
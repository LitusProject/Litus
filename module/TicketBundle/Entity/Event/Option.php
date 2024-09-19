<?php

namespace TicketBundle\Entity\Event;

use Doctrine\ORM\Mapping as ORM;
use TicketBundle\Entity\Event;

/**
 * @ORM\Entity(repositoryClass="TicketBundle\Repository\Event\Option")
 * @ORM\Table(name="ticket_events_options")
 */
class Option
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
     * @ORM\ManyToOne(targetEntity="TicketBundle\Entity\Event", inversedBy="tickets")
     * @ORM\JoinColumn(name="event", referencedColumnName="id")
     */
    private $event;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var integer The price for members
     *
     * @ORM\Column(name="price_members", type="integer")
     */
    private $priceMembers;

    /**
     * @var integer The price for non members
     *
     * @ORM\Column(name="price_non_members", type="integer", nullable=true)
     */
    private $priceNonMembers;

    /**
     * @var integer The maximum tickets for this option
     *
     * @ORM\Column(name="maximum", type="integer", nullable=true)
     */
    private $maximum;

    /**
     * @var boolean Whether this option is visible or not
     *
     * @ORM\Column(name="visible", type="boolean", nullable=true)
     */
    private $visible;

    /**
     * @var integer The maximum amount of tickets for an option per person
     *
     * @ORM\Column(name="limit_per_person_option", type="integer", nullable=true)
     */
    private $limitPerPerson;

    /**
     * @param Event               $event
     * @param string              $name
     * @param integer             $priceMembers
     * @param integer|null        $priceNonMembers
     * @param integer|string|null $maximum
     * @param integer|null        $limit
     */
    public function __construct(Event $event, $name, $priceMembers, $priceNonMembers, $maximum, $visible, $limit)
    {
        $this->event = $event;
        $this->name = $name;
        $this->maximum = $maximum;
        $this->visible = $visible;
        $this->limitPerPerson = $limit;

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
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param  string $name
     * @return Option
     */
    public function setName($name)
    {
        $this->name = $name;

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
     * @param  integer $max
     * @return self
     */
    public function setMaximum($max)
    {
        $this->maximum = $max;

        return $this;
    }

    /**
-     * @return integer
     */
    public function getMaximum()
    {
        return $this->maximum;
    }

    /**
     * @return boolean
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * @param boolean $visible
     * @return self
     */
    public function setIsVisible($visible)
    {
        $this->visible = $visible;
        return $this;
    }

    /**
     * @return integer|null
     */
    public function getLimitPerPerson()
    {
        return $this->limitPerPerson;
    }

    /**
     * @param $limit
     * @return self
     */
    public function setLimitPerPerson($limit)
    {
        $this->limitPerPerson = $limit;
        return $this;
    }
}

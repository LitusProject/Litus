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
     * @var integer The maximum tickets for this option
     *
     * @ORM\Column(name="maximum", type="integer", nullable=true)
     */
    private $maximum;

    /**
     * @param Event               $event
     * @param string              $name
     * @param integer             $priceMembers
     * @param integer             $priceNonMembers
     * @param integer|string|null $maximum
     */
    public function __construct(Event $event, $name, $priceMembers, $priceNonMembers, $maximum)
    {
        $this->event = $event;
        $this->name = $name;
        $this->maximum = $maximum;

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
}

<?php

namespace BrBundle\Entity\Company;

use BrBundle\Entity\Company;
use CalendarBundle\Entity\Node\Event as CommonEvent;
use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for an event.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Company\Event")
 * @ORM\Table(name="br_companies_events")
 */
class Event
{
    /**
     * @var string The company event's ID
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var \CalendarBundle\Entity\Node\Event The event
     *
     * @ORM\OneToOne(targetEntity="CalendarBundle\Entity\Node\Event", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="event", referencedColumnName="id")
     */
    private $event;

    /**
     * @var \BrBundle\Entity\Company The company of the event
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Company")
     * @ORM\JoinColumn(name="company", referencedColumnName="id")
     */
    private $company;

    /**
     * @param \CalendarBundle\Entity\Node\Event $event
     * @param \BrBundle\Entity\Company          $company The event's company
     */
    public function __construct(CommonEvent $event, Company $company)
    {
        $this->event = $event;
        $this->company = $company;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \CalendarBundle\Entity\Node\Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return \BrBundle\Entity\Company
     */
    public function getCompany()
    {
        return $this->company;
    }
}

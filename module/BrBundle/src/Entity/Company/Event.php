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

namespace BrBundle\Entity\Company;

use BrBundle\Entity\Company,
    CalendarBundle\Entity\Nodes\Event as CommonEvent,
    DateTime,
    Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for an event.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Company\Event")
 * @ORM\Table(name="br.companies_events")
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
     * @var string The event
     *
     * @ORM\OneToOne(targetEntity="CalendarBundle\Entity\Nodes\Event", cascade={"persist", "remove"})
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
     * @param \CalendarBundle\Entity\Nodes\Event $event
     * @param \BrBundle\Entity\Company $company The event's company
     */
    public function __construct(CommonEvent $event, Company $company)
    {
        $this->event = $event;
        $this->company = $company;
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

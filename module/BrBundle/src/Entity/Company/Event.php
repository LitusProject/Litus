<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Entity\Company;

use BrBundle\Entity\Company,
    CalendarBundle\Entity\Nodes\Event as CommonEvent,
    DateTime;

/**
 * This is the entity for an event.
 *
 * @Entity(repositoryClass="BrBundle\Repository\Company\Event")
 * @Table(name="br.companies_events")
 */
class Event
{
    /**
     * @var string The company event's ID
     *
     * @Id
     * @Column(type="bigint")
     * @GeneratedValue
     */
    private $id;

    /**
     * @var string The event
     *
     *
     * @OneToOne(targetEntity="CalendarBundle\Entity\Nodes\Event", cascade={"persist", "remove"})
     * @JoinColumn(name="event", referencedColumnName="id")
     */
    private $event;

    /**
     * @var \BrBundle\Entity\Company The company of the event
     *
     * @OneToOne(targetEntity="BrBundle\Entity\Company")
     * @JoinColumn(name="company", referencedColumnName="id")
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
     * @return \BrBundle\Entity\Company
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

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

namespace TicketBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @var string The activity of the event
     *
     * @ORM\OneToOne(targetEntity="CalendarBundle\Entity\Nodes\Activity")
     * @ORM\JoinColumn(name="activity", referencedColumnName="id")
     */
    private $activity;

    /**
     * @var boolean Flag whether the tickets are bookable
     *
     * @ORM\Column(type="boolean")
     */
    private $bookable;

    /**
     * @var \DateTime The date the booking system will close
     *
     * @ORM\Column(name="bookings_close_date", type="datetime")
     */
    private $bookingsCloseDate;

    /**
     * @var boolean Flag whether the event booking system is active
     *
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @var integer The maximum number of tickets bookable by one person
     *
     * @ORM\Column(name="limit_per_person", type="integer")
     */
    private $limitPerPerson;

    /**
     * @var integer The total number of tickets
     *
     * @ORM\Column(name="number_of_tickets", type="integer")
     */
    private $numberOfTickets;

    /**
     * @var boolean Flag whether the tickets are generated
     *
     * @ORM\Column(name="tickets_generated", type="boolean")
     */
    private $ticketsGenerated;
}

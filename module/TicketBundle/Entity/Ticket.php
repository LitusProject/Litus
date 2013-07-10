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
 * @ORM\Entity(repositoryClass="TicketBundle\Repository\Ticket")
 * @ORM\Table(
 *     name="tickets.tickets",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="ticket_number_unique", columns={"event", "number"})
 *      }
 * )
 */
class Ticket
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
     * @var string The event of the ticket
     *
     * @ORM\OneToOne(targetEntity="TicketBundle\Entity\Event")
     * @ORM\JoinColumn(name="event", referencedColumnName="id")
     */
    private $event;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $status;

    /**
     * @var \CommonBundle\Entity\Users\Person The person how bought/reserved the ticket
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\Users\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var \DateTime The date the ticket was booked
     *
     * @ORM\Column(name="book_date", type="datetime")
     */
    private $bookDate;

    /**
     * @var \DateTime The date the ticket was sold
     *
     * @ORM\Column(name="sold_date", type="datetime")
     */
    private $soldDate;

    /**
     * @var integer The number of the ticket (unique for an event)
     *
     * @ORM\Column(type="bigint")
     */
    private $number;

    /**
     * @var array The possible states of a ticket
     */
    private static $POSSIBLE_STATUSES = array(
        'emtpy' => 'Empty',
        'booked' => 'Booked',
        'sold' => 'Sold',
    );
}

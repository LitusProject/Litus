<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 10/4/18
 * Time: 2:23 PM
 */

namespace TicketBundle\Entity;

use CommonBundle\Entity\User\Person,
    DateTime,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="TicketBundle\Repository\Ticket")
 * @ORM\Table(name="tickets.tickets")
 */
class Ticket
{

    /**
     * @var array The possible states of a ticket
     */
    const POSSIBLE_STATUSES = array(
        'empty'  => 'Empty',
        'booked' => 'Booked',
        'sold'   => 'Sold',
    );

    /**
     * @var integer The ID of the ticket
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The status of the ticket, see above
     *
     * @ORM\Column(type="string")
     */
    private $status;

    /**
     * @var Order The order that the ticket is part of
     *
     * @ORM\ManyToOne(targetEntity="TicketBundle\Entity\Order", inversedBy="tickets")
     * @ORM\JoinColumn(name="order", referencedColumnName="id")
     */
    private $order;

    /**
     * @var Person|null The person who bought/reserved the order
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var DateTime|null The date the ticket was booked
     *
     * @ORM\Column(name="book_date", type="datetime", nullable=true)
     */
    private $bookDate;

    /**
     * @var DateTime|null The date the ticket was sold
     *
     * @ORM\Column(name="sold_date", type="datetime", nullable=true)
     */
    private $soldDate;

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
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param Order $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return null|Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @param null|Person $person
     */
    public function setPerson($person)
    {
        $this->person = $person;
    }

    /**
     * @return null|DateTime
     */
    public function getBookDate()
    {
        return $this->bookDate;
    }

    /**
     * @param null|DateTime $bookDate
     */
    public function setBookDate($bookDate)
    {
        $this->bookDate = $bookDate;
    }

    /**
     * @return null|DateTime
     */
    public function getSoldDate()
    {
        return $this->soldDate;
    }

    /**
     * @param null|DateTime $soldDate
     */
    public function setSoldDate($soldDate)
    {
        $this->soldDate = $soldDate;
    }


}
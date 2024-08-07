<?php

namespace LogisticsBundle\Entity;

use CommonBundle\Entity\User\Person;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use LogisticsBundle\Entity\Lease\Item;

/**
 * The entity for the lease of an item
 *
 * @ORM\Entity(repositoryClass="LogisticsBundle\Repository\Lease")
 * @ORM\Table(name="logistics_leases")
 */
class Lease
{
    /**
     * @var integer The lease's ID
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Item The leased item
     *
     * @ORM\ManyToOne(targetEntity="LogisticsBundle\Entity\Lease\Item")
     * @ORM\JoinColumn(name="item", referencedColumnName="id", nullable=false)
     */
    private $item;

    /**
     * @var integer The number of items that was leased
     *
     * @ORM\Column(name="leased_amount", type="integer")
     */
    private $leasedAmount;

    /**
     * @var DateTime The date the item was leased
     *
     * @ORM\Column(name="leased_date", type="datetime")
     */
    private $leasedDate;

    /**
     * @var Person The person who handed the item out
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="leased_by", referencedColumnName="id", nullable=false)
     */
    private $leasedBy;

    /**
     * @var string The person who received the handed-out item
     *
     * @ORM\Column(name="leased_to", type="text")
     */
    private $leasedTo;

    /**
     * @var integer The pawn the person paid for the leased item in cents
     *
     * @ORM\Column(name="leased_pawn", type="bigint")
     */
    private $leasedPawn;

    /**
     * @var string Additional information about the lease
     *
     * @ORM\Column(name="leased_comment", type="text", nullable=true)
     */
    private $leasedComment;

    /**
     * @var boolean Flag whether the item was already returned
     *
     * @ORM\Column(name="returned", type="boolean")
     */
    private $returned;

    /**
     * @var integer The number of items that was leased
     *
     * @ORM\Column(name="returned_amount", type="integer")
     */
    private $returnedAmount;

    /**
     * @var DateTime|null The date when the item was returned
     *
     * @ORM\Column(name="returned_date", type="datetime", nullable=true)
     */
    private $returnedDate;

    /**
     * @var string|null The person who returned the item
     *
     * @ORM\Column(name="returned_by", type="string", nullable=true)
     */
    private $returnedBy;

    /**
     * @var Person|null The person the item was returned to
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="returned_to", referencedColumnName="id", nullable=true)
     */
    private $returnedTo;

    /**
     * @var integer|null The pawn the person got back for returning the item in cents
     *
     * @ORM\Column(name="returned_pawn", type="bigint", nullable=true)
     */
    private $returnedPawn;

    /**
     * @var string|null Additional information about the return
     *
     * @ORM\Column(name="returned_comment", type="text", nullable=true)
     */
    private $returnedComment;

    /**
     * @param Item        $item          The leased item
     * @param integer     $leasedAmount  The number of items that was leased
     * @param DateTime    $leasedDate    The date of the lease
     * @param Person      $leasedBy      The person who handed out the item
     * @param string      $leasedTo      The person who received the item
     * @param integer     $leasedPawn    The pawn paid for the item
     * @param string|null $leasedComment An optional comment for the lease
     */
    public function __construct(Item $item, $leasedAmount, DateTime $leasedDate, Person $leasedBy, $leasedTo, $leasedPawn, $leasedComment = null)
    {
        $this->item = $item;

        $this->leasedAmount = $leasedAmount;
        $this->leasedDate = $leasedDate;
        $this->leasedBy = $leasedBy;
        $this->leasedTo = $leasedTo;
        $this->leasedPawn = (int) ($leasedPawn * 100);
        $this->leasedComment = $leasedComment;

        $this->returned = false;
        $this->returnedAmount = 0;
        $this->returnedDate = null;
        $this->returnedBy = null;
        $this->returnedTo = null;
        $this->returnedPawn = null;
        $this->returnedComment = null;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @return integer
     */
    public function getLeasedAmount()
    {
        return $this->leasedAmount;
    }

    /**
     * @param  integer $leasedAmount
     * @return self
     */
    public function setLeasedAmount($leasedAmount)
    {
        $this->leasedAmount = $leasedAmount;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getLeasedDate()
    {
        return $this->leasedDate;
    }

    /**
     * @return Person
     */
    public function getLeasedBy()
    {
        return $this->leasedBy;
    }

    /**
     * @return string
     */
    public function getLeasedTo()
    {
        return $this->leasedTo;
    }

    /**
     * @return integer
     */
    public function getLeasedPawn()
    {
        return $this->leasedPawn / 100;
    }

    /**
     * @return string
     */
    public function getLeasedComment()
    {
        return $this->leasedComment;
    }

    /**
     * @return boolean
     */
    public function isReturned()
    {
        return $this->returned;
    }

    /**
     * @param  boolean $returned
     * @return self
     */
    public function setReturned($returned)
    {
        $this->returned = $returned;

        return $this;
    }

    /**
     * @return integer
     */
    public function getReturnedAmount()
    {
        return $this->returnedAmount;
    }

    /**
     * @param  integer $returnedAmount
     * @return self
     */
    public function setReturnedAmount($returnedAmount)
    {
        $this->returnedAmount = $returnedAmount;

        return $this;
    }

    /**
     * @param  DateTime $returnedDate
     * @return self
     */
    public function setReturnedDate(\DateTime $returnedDate)
    {
        $this->returnedDate = $returnedDate;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getReturnedDate()
    {
        return $this->returnedDate;
    }

    /**
     * @param  string $returnedBy
     * @return self
     */
    public function setReturnedBy($returnedBy)
    {
        $this->returnedBy = $returnedBy;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getReturnedBy()
    {
        return $this->returnedBy;
    }

    /**
     * @param  Person $returnedTo
     * @return self
     */
    public function setReturnedTo(Person $returnedTo)
    {
        $this->returnedTo = $returnedTo;

        return $this;
    }

    /**
     * @return Person|null
     */
    public function getReturnedTo()
    {
        return $this->returnedTo;
    }

    /**
     * @param  float $returnedPawn
     * @return self
     */
    public function setReturnedPawn($returnedPawn)
    {
        $this->returnedPawn = (int) ($returnedPawn * 100);

        return $this;
    }

    /**
     * @return float|integer
     */
    public function getReturnedPawn()
    {
        if ($this->returnedPawn === null) {
            return 0;
        }

        return (float) $this->returnedPawn / 100;
    }

    /**
     * @param  string $returnedComment
     * @return self
     */
    public function setReturnedComment($returnedComment)
    {
        $this->returnedComment = $returnedComment;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getReturnedComment()
    {
        return $this->returnedComment;
    }
}

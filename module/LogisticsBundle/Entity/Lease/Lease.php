<?php

namespace LogisticsBundle\Entity\Lease;

use Doctrine\ORM\Mapping as ORM,
    CommonBundle\Entity\User\Person;

/**
 * The entity for the lease of an item
 *
 * @ORM\Entity(repositoryClass="LogisticsBundle\Repository\Lease\Lease")
 * @ORM\Table(name="logistics.lease_lease")
 */
class Lease
{
    /**
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * The leased item
     * @var \LogisticsBundle\Entity\Lease\Item
     *
     * @ORM\ManyToOne(targetEntity="Item")
     * @ORM\JoinColumn(name="item", referencedColumnName="id", nullable=false)
     */
    private $item;

    /**
     * The date the item was leased
     * @var \DateTime
     *
     * @ORM\Column(name="leased_date", type="datetime")
     */
    private $leasedDate;

    /**
     * The person who handed the item out
     * @var \CommonBundle\Entity\User\Person
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="leased_by", referencedColumnName="id", nullable=false)
     */
    private $leasedBy;

    /**
     * The person who received the handed-out item
     * @var string
     *
     * @ORM\Column(name="leased_to", type="text")
     */
    private $leasedTo;

    /**
     * The pawn the person paid for the leased item in eurocents
     * @var int
     *
     * @ORM\Column(name="leased_pawn", type="bigint")
     */
    private $leasedPawn;

    /**
     * Flag whether the item was already returned
     * @var boolean
     *
     * @ORM\Column(name="returned", type="boolean")
     */
    private $returned;

    /**
     * The date when the item was returned
     * @var \DateTime
     *
     * @ORM\Column(name="returned_date", type="datetime", nullable=true)
     */
    private $returnedDate;

    /**
     * The person who returned the item
     * @var string
     *
     * @ORM\Column(name="returned_by", type="string", nullable=true)
     */
    private $returnedBy;

    /**
     * The person the item was returned to
     * @var \CommonBundle\Entity\User\Person
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="returned_to", referencedColumnName="id", nullable=true)
     */
    private $returnedTo;

    /**
     * The pawn the person got back for returning the item in eurocents
     * @var int
     *
     * @ORM\Column(name="returned_pawn", type="bigint", nullable=true)
     */
    private $returnedPawn;

    /**
     *
     * @param \LogisticsBundle\Entity\Lease\Item $item The leased item
     * @param \DateTime $leasedDate The date of the lease
     * @param \CommonBundle\Entity\User\Person $leasedBy The person who handed out the item
     * @param string $leasedTo The person who received the item
     * @param int $leasedPawn The pawn paid for the item (in euros)
     */
    public function __construct(Item $item, \DateTime $leasedDate, Person $leasedBy, $leasedTo, $leasedPawn)
    {
        $this->item = $item;
        $this->leasedDate = $leasedDate;
        $this->leasedBy = $leasedBy;
        $this->leasedTo = $leasedTo;
        $this->leasedPawn = $leasedPawn * 100;
        $this->returned = false;
        $this->returnedDate = null;
        $this->returnedBy = null;
        $this->returnedTo = null;
        $this->returnedPawn = null;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get leasedDate
     *
     * @return \DateTime
     */
    public function getLeasedDate()
    {
        return $this->leasedDate;
    }

    /**
     * Get leasedBy
     *
     * @return string
     */
    public function getLeasedBy()
    {
        return $this->leasedBy;
    }

    /**
     * Get leasedTo
     *
     * @return string
     */
    public function getLeasedTo()
    {
        return $this->leasedTo;
    }

    /**
     * Get leasedPawn
     *
     * @return float
     */
    public function getLeasedPawn()
    {
        return $this->leasedPawn/100;
    }

    /**
     * Set returned
     *
     * @param boolean $returned
     * @return Lease
     */
    public function setReturned($returned)
    {
        $this->returned = $returned;

        return $this;
    }

    /**
     * Get returned
     *
     * @return boolean
     */
    public function isReturned()
    {
        return $this->returned;
    }

    /**
     * Set returnedDate
     *
     * @param \DateTime $returnedDate
     * @return Lease
     */
    public function setReturnedDate(\DateTime $returnedDate)
    {
        $this->returnedDate = $returnedDate;

        return $this;
    }

    /**
     * Get returnedDate
     *
     * @return \DateTime|null
     */
    public function getReturnedDate()
    {
        return $this->returnedDate;
    }

    /**
     * Set returnedBy
     *
     * @param string $returnedBy
     * @return Lease
     */
    public function setReturnedBy($returnedBy)
    {
        $this->returnedBy = $returnedBy;

        return $this;
    }

    /**
     * Get returnedBy
     *
     * @return string|null
     */
    public function getReturnedBy()
    {
        return $this->returnedBy;
    }

    /**
     * Set returnedTo
     *
     * @param \CommonBundle\Entity\User\Person $returnedTo
     * @return Lease
     */
    public function setReturnedTo(Person $returnedTo)
    {
        $this->returnedTo = $returnedTo;

        return $this;
    }

    /**
     * Get returnedTo
     *
     * @return \CommonBundle\Entity\User\Person|null
     */
    public function getReturnedTo()
    {
        return $this->returnedTo;
    }

    /**
     * Set returnedPawn
     *
     * @param integer $returnedPawn
     * @return Lease
     */
    public function setReturnedPawn($returnedPawn)
    {
        $this->returnedPawn = $returnedPawn;

        return $this;
    }

    /**
     * Get returnedPawn
     *
     * @return float
     */
    public function getReturnedPawn()
    {
        return $this->returnedPawn/100;
    }

    /**
     * Get item
     *
     * @return \LogisticsBundle\Entity\Lease\Item
     */
    public function getItem()
    {
        return $this->item;
    }
}
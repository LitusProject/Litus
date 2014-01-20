<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

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
     * @var integer The lease's ID
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \LogisticsBundle\Entity\Lease\Item The leased item
     *
     * @ORM\ManyToOne(targetEntity="Item")
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
     * @var \DateTime The date the item was leased
     *
     * @ORM\Column(name="leased_date", type="datetime")
     */
    private $leasedDate;

    /**
     * @var \CommonBundle\Entity\User\Person The person who handed the item out
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
     * @var int The pawn the person paid for the leased item in cents
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
     * @var \DateTime The date when the item was returned
     *
     * @ORM\Column(name="returned_date", type="datetime", nullable=true)
     */
    private $returnedDate;

    /**
     * @var string The person who returned the item
     *
     * @ORM\Column(name="returned_by", type="string", nullable=true)
     */
    private $returnedBy;

    /**
     * @var \CommonBundle\Entity\User\Person The person the item was returned to
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="returned_to", referencedColumnName="id", nullable=true)
     */
    private $returnedTo;

    /**
     * @var int The pawn the person got back for returning the item in cents
     *
     * @ORM\Column(name="returned_pawn", type="bigint", nullable=true)
     */
    private $returnedPawn;

    /**
     * @var string Additional information about the return
     *
     * @ORM\Column(name="returned_comment", type="text", nullable=true)
     */
    private $returnedComment;

    /**
     * @param \LogisticsBundle\Entity\Lease\Item $item The leased item
     * @param int $leasedAmount The number of items that was leased
     * @param \DateTime $leasedDate The date of the lease
     * @param \CommonBundle\Entity\User\Person $leasedBy The person who handed out the item
     * @param string $leasedTo The person who received the item
     * @param int $leasedPawn The pawn paid for the item
     * @param string $leasedComment An optional comment for the lease
     */
    public function __construct(Item $item, $leasedAmount, \DateTime $leasedDate, Person $leasedBy, $leasedTo, $leasedPawn, $leasedComment = null)
    {
        $this->item = $item;

        $this->leasedAmount = $leasedAmount;
        $this->leasedDate = $leasedDate;
        $this->leasedBy = $leasedBy;
        $this->leasedTo = $leasedTo;
        $this->leasedPawn = $leasedPawn * 100;
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
     * @return \LogisticsBundle\Entity\Lease\Item
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
     * @param integer $leasedAmount
     * @return \LogisticsBundle\Entity\Lease\Lease
     */
    public function setLeasedAmount($leasedAmount)
    {
        $this->leasedAmount = $leasedAmount;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLeasedDate()
    {
        return $this->leasedDate;
    }

    /**
     * @return \CommonBundle\Entity\User\Person
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
     * @return float
     */
    public function getLeasedPawn()
    {
        return $this->leasedPawn/100;
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
     * @param boolean $returned
     * @return \LogisticsBundle\Entity\Lease\Lease
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
     * @param integer $returnedAmount
     * @return \LogisticsBundle\Entity\Lease\Lease
     */
    public function setReturnedAmount($returnedAmount)
    {
        $this->returnedAmount = $returnedAmount;
        return $this;
    }

    /**
     * @param \DateTime $returnedDate
     * @return \LogisticsBundle\Entity\Lease\Lease
     */
    public function setReturnedDate(\DateTime $returnedDate)
    {
        $this->returnedDate = $returnedDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getReturnedDate()
    {
        return $this->returnedDate;
    }

    /**
     * @param string $returnedBy
     * @return \LogisticsBundle\Entity\Lease\Lease
     */
    public function setReturnedBy($returnedBy)
    {
        $this->returnedBy = $returnedBy;

        return $this;
    }

    /**
     * @return string
     */
    public function getReturnedBy()
    {
        return $this->returnedBy;
    }

    /**
     * @param \CommonBundle\Entity\User\Person $returnedTo
     * @return \LogisticsBundle\Entity\Lease\Lease
     */
    public function setReturnedTo(Person $returnedTo)
    {
        $this->returnedTo = $returnedTo;
        return $this;
    }

    /**
     * @return \CommonBundle\Entity\User\Person|null
     */
    public function getReturnedTo()
    {
        return $this->returnedTo;
    }

    /**
     * @param float $returnedPawn
     * @return \LogisticsBundle\Entity\Lease\Lease
     */
    public function setReturnedPawn($returnedPawn)
    {
        $this->returnedPawn = $returnedPawn*100;

        return $this;
    }

    /**
     * @return float
     */
    public function getReturnedPawn()
    {
        return $this->returnedPawn/100;
    }

    /**
     * @param string $returnedComment
     * @return \LogisticsBundle\Entity\Lease\Lease
     */
    public function setReturnedComment($returnedComment)
    {
        $this->returnedComment = $returnedComment;
        return $this;
    }

    /**
     * @return string
     */
    public function getReturnedComment()
    {
        return $this->returnedComment;
    }
}
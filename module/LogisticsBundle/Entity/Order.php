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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace LogisticsBundle\Entity;

use CommonBundle\Entity\General\Location;
use CommonBundle\Entity\General\Organization\Unit;
use CommonBundle\Entity\User\Person;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/**
 * This is the entity for an order.
 *
 * @ORM\Entity(repositoryClass="LogisticsBundle\Repository\Order")
 * @ORM\Table(name="logistics_order")
 */
class Order
{
    /**
     * @var integer The order's ID
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string The order's name
     *
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @var string The description of the order
     *
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @var string Internal Comment
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $internalComment;

    /**
     * @var string External Comment
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $externalComment;

    /**
     * @var string The mail-address for the contact for this order
     *
     * @ORM\Column(name="email", type="text", nullable=true)
     */
    private $email;

    /**
     * @var Location the location of the order
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Location")
     * @ORM\JoinColumn(name="location", referencedColumnName="id")
     */
    private $location;

    /**
     * @var string The contact used in this order
     *
     * @ORM\Column(name="contact", type="text", nullable=true)
     */
    private $contact;

    /**
     * @var Person The creator used in this order
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="creator", referencedColumnName="id", nullable =true)
     */
    private $creator;

    /**
     * @var Unit The unit of the order
     *
     * @ORM\ManyToOne(targetEntity="\CommonBundle\Entity\General\Organization\Unit")
     * @ORM\JoinColumn(name="unit", referencedColumnName="id", nullable=true)
     */
    private $unit;

    /**
     * @var DateTime The last time this order was updated.
     *
     * @ORM\Column(type="datetime")
     */
    private $dateUpdated;

    /**
     * @var DateTime The start date and time of this order.
     *
     * @ORM\Column(name="start_date", type="datetime")
     */
    private $startDate;

    /**
     * @var DateTime The end date and time of this order.
     *
     * @ORM\Column(name="end_date", type="datetime")
     */
    private $endDate;

    /**
     * @var boolean If this order has been approved by our Logistics team
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $approved;

    /**
     * @var boolean If this order has been removed.
     *
     * @ORM\Column(type="boolean", options={"default" = false})
     */
    private $removed;

    /**
     * @var boolean If this order has been rejected.
     *
     * @ORM\Column(type="boolean", options={"default" = false})
     */
    private $rejected;

    /**
     * @var boolean If this order needs a van ride (een kar-rit).
     *
     * @ORM\Column(name="needs_ride", type="boolean", options={"default" = false})
     */
    private $needsRide;

    /**
     * @param string $contact
     */
    public function __construct($contact)
    {
        $this->contact = $contact;
        $this->dateUpdated = new DateTime();
        $this->removed = false;
        $this->rejected = false;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        if ($this->isRemoved()) {
            return 'Removed';
        }
        if ($this->isRejected()) {
            return 'Rejected';
        }
        if ($this->isApproved()) {
            return 'Approved';
        }
        return 'Pending';
    }

    /**
     * @param string $status
     * @return self
     */
    public function setStatus(string $status)
    {
        if ($status == 'removed') {
            $this->remove();
        }
        if ($status == 'rejected') {
            $this->reject();
        } elseif ($status == 'approved') {
            $this->approve();
        } elseif ($status == 'pending') {
            $this->pending();
        }
        return $this;
    }

    /**
     * @return self
     */
    public function approve()
    {
        $this->approved = true;
        $this->rejected = false;
        $this->removed = false;

        return $this;
    }

    /**
     * @return self
     */
    public function pending()
    {
        $this->approved = false;
        $this->rejected = false;
        $this->removed = false;
        return $this;
    }

    /**
     * @return self
     */
    public function remove()
    {
        $this->approved = false;
        $this->rejected = false;
        $this->removed = true;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isRejected(): bool
    {
        return $this->rejected;
    }

    /**
     * @return self
     */
    public function reject()
    {
        $this->rejected = true;
        $this->removed = false;
        $this->approved = false;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isRemoved()
    {
        return $this->removed;
    }

    /**
     * @return boolean
     */
    public function isApproved()
    {
        if ($this->approved === null) {
            return false;
        }

        return $this->approved;
    }

    /**
     * @return boolean
     */
    public function isPending()
    {
        if ($this->approved === null) {
            return true;
        }

        return !$this->approved && !$this->rejected && !$this->removed;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param  string $name
     * @return Order
     */
    public function setName($name)
    {
        if ($name === null || !is_string($name)) {
            throw new InvalidArgumentException('Invalid name');
        }

        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param  string $description
     * @return Order
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param  string $internalComment
     * @return Order
     */
    public function setInternalComment($internalComment)
    {
        $this->internalComment = $internalComment;

        return $this;
    }

    /**
     * @return string
     */
    public function getInternalComment()
    {
        return $this->internalComment;
    }

    /**
     * @param  string $externalComment
     * @return Order
     */
    public function setExternalComment($externalComment)
    {
        $this->externalComment = $externalComment;

        return $this;
    }

    /**
     * @return string
     */
    public function getExternalComment()
    {
        return $this->externalComment;
    }

    /**
     * @param  string $email
     * @return Order
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return boolean
     */
    public function needsRide()
    {
        return $this->needsRide;
    }

    /**
     * @param boolean $b
     * @return boolean
     */
    public function setNeedsRide(bool $b)
    {
        return $this->needsRide = $b;
    }

    /**
     * @return Unit
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param  Unit $unit
     * @return Order
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * @return Order
     */
    public function updateDate()
    {
        $this->dateUpdated = new DateTime();

        return $this;
    }

    /**
     * @return DateTime The last time this order was updated.
     */
    public function getLastUpdateDate()
    {
        return $this->dateUpdated;
    }

    /**
     * @param  DateTime $startDate
     * @return Order
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param  DateTime $endDate
     * @return Order
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param Location $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param string $contact
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
    }

    /**
     * @return boolean
     */
    public function isCancellable()
    {
        return(!$this->isRemoved() && !$this->isPending());
    }

    /**
     * @return boolean
     */
    public function isEditable()
    {
        return !$this->isRemoved() && !$this->isRejected();
    }

    /**
     * @return Person
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * @param Person $creator
     */
    public function setCreator(Person $creator)
    {
        $this->creator = $creator;
    }
}

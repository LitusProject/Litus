<?php

namespace LogisticsBundle\Entity;

use CommonBundle\Entity\General\Location;
use CommonBundle\Entity\General\Organization\Unit;
use CommonBundle\Entity\User\Person;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use LogisticsBundle\Entity\Request;

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
    private string $name;

    /**
     * @var string The description of the order
     *
     * @ORM\Column(type="text")
     */
    private string $description;

    /**
     * @var string|null Internal Comment
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $internalComment = '';

    /**
     * @var string|null External Comment
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $externalComment = '';

    /**
     * @var string The mail-address for the contact for this order
     *
     * @ORM\Column(name="email", type="text", nullable=true)
     */
    private string $email;

    /**
     * @var Location the location of the order
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Location")
     * @ORM\JoinColumn(name="location", referencedColumnName="id")
     */
    private Location $location;

//    /**
//     * @var Academic The contact used in this order
//     *
//     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person\Academic")
//     * @ORM\JoinColumn(name="contact", referencedColumnName="id")
//     */
//    private $contact;

    /**
     * @var string The contact name used in this order
     *
     * @ORM\Column(name="contact", type="text", nullable=true)
     */
    private string $contact;

    /**
     * @var Person The creator used in this order
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="creator", referencedColumnName="id", nullable =true)
     */
    private Person $creator;

    /**
     * @var ArrayCollection The units associated with the order: gives access to the whole unit to view the order
     *
     * @ORM\ManyToMany(targetEntity="\CommonBundle\Entity\General\Organization\Unit")
     * @ORM\JoinTable(
     *       name="order_unit",
     *       joinColumns={@ORM\JoinColumn(name="order_id", referencedColumnName="id")},
     *       inverseJoinColumns={@ORM\JoinColumn(name="unit_id", referencedColumnName="id")}
     *  )
     */
    private Collection $units;

    /**
     * @var DateTime The last time this order was updated.
     *
     * @ORM\Column(type="datetime")
     */
    private DateTime $dateUpdated;

    /**
     * @var string The person who updated this particular order
     *
     * @ORM\Column(name="updator", type="text", nullable=true)
     */
    private string $updator;

    /**
     * @var DateTime The start date and time of this order.
     *
     * @ORM\Column(name="start_date", type="datetime")
     */
    private DateTime $startDate;

    /**
     * @var DateTime The end date and time of this order.
     *
     * @ORM\Column(name="end_date", type="datetime")
     */
    private DateTime $endDate;

    /**
     * @var boolean If this order has been approved by our Logistics team
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private bool $approved;

    /**
     * @var boolean If this order has been removed.
     *
     * @ORM\Column(type="boolean", options={"default" = false})
     */
    private bool $removed;

    /**
     * @var boolean If this order has been rejected.
     *
     * @ORM\Column(type="boolean", options={"default" = false})
     */
    private bool $rejected;

    /**
     * @var boolean If this order has been reviewed.
     *
     * @ORM\Column(type="boolean", options={"default" = false})
     */
    private bool $reviewed;

    /**
     * @var boolean If this order has been canceled.
     *
     * @ORM\Column(type="boolean", options={"default" = false})
     */
    private bool $canceled;

    /**
     * @var boolean If this order needs a ride (een kar-rit, auto-rit of dergelijke).
     *
     * @ORM\Column(name="needs_ride", type="boolean", options={"default" = false}, nullable=true)
     */
    private bool $needsRide;

    /**
     * @var Request The Request of the mapping
     *
     * @ORM\ManyToOne(targetEntity="LogisticsBundle\Entity\Request", cascade={"persist"})
     * @ORM\JoinColumn(name="referenced_Request", referencedColumnName="id", onDelete="CASCADE")
     */
    private Request $referencedRequest;

    /**
     * @param string $contact
     * @param Request|null $request
     * @param string $updator
     * @param string $status
     */
    public function __construct(string $contact, ?Request $request, string $updator, string $status = '')
    {
        $this->contact = $contact;
        $this->dateUpdated = new DateTime();
        $this->units = new ArrayCollection();
        $this->updator = $updator;
        $this->removed = false;
        $this->rejected = false;
        $this->referencedRequest = $request;
        $this->setStatus($status);
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->referencedRequest;
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
        if ($this->isReviewed()) {
            return 'Reviewed';
        }
        if ($this->isCanceled()) {
            return 'Canceled';
        }
        return 'Pending';               // Default is pending
    }

    /**
     * @param string $status
     * @return self
     */
    public function setStatus(string $status)
    {
        if ($status === 'removed') {
            $this->remove();
        }
        if ($status === 'rejected') {
            $this->reject();
        } elseif ($status === 'approved') {
            $this->approve();
        } elseif ($status === 'reviewed') {
            $this->review();
        } elseif ($status === 'canceled') {
            $this->cancel();
        }
        return $this->pending();        // Default is pending
    }

    /**
     * @return self
     */
    public function approve()
    {
        $this->approved = true;
        $this->rejected = false;
        $this->reviewed = false;

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
        $this->reviewed = false;
        $this->canceled = false;

        return $this;
    }

    /**
     * @return self
     */
    public function review()
    {
        $this->approved = false;
        $this->rejected = false;
        $this->reviewed = true;

        return $this;
    }

    /**
     * @return self
     */
    public function reject()
    {
        $this->rejected = true;
        $this->approved = false;
        $this->reviewed = false;

        return $this;
    }

    /**
     * @return self
     */
    public function cancel()
    {
        $this->rejected = false;
        $this->approved = false;
        $this->reviewed = false;
        $this->canceled = true;

        return $this;
    }

    /**
     * @return self
     */
    public function remove()
    {
        $this->canceled = false;
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
     * @return boolean
     */
    public function isReviewed()
    {
        if ($this->reviewed === null) {
            return false;
        }

        return $this->reviewed;
    }

    /**
     * @return boolean
     */
    public function isCanceled()
    {
        if ($this->canceled === null) {
            return false;
        }

        return $this->canceled;
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
    public function setInternalComment($internalComment): self
    {
        $this->internalComment = $internalComment;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getInternalComment(): ?string
    {
        return $this->internalComment;
    }

    /**
     * @param  string $externalComment
     * @return Order
     */
    public function setExternalComment($externalComment): self
    {
        $this->externalComment = $externalComment;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getExternalComment(): ?string
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
     * @return Collection
     */
    public function getUnits(): Collection
    {
        return $this->units;
    }

    /**
     * @param  Collection $units
     * @return Order
     */
    public function setUnits(Collection $units): self
    {
        $this->units = $units;

        return $this;
    }

    /**
     * @param  Unit $unit
     * @return Order
     */
    public function addUnit(Unit $unit): self
    {
        if (!$this->units->contains($unit)) {
            $this->units->add($unit);
        }

        return $this;
    }

    /**
     * @param Unit $unit
     * @return Order
     */
    public function removeUnit(Unit $unit): self
    {
        if ($this->units->contains($unit)) {
            $this->units->removeElement($unit);
        }

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
    public function getUpdateDate()
    {
        return $this->dateUpdated;
    }

    /**
     * @return string
     */
    public function getUpdator()
    {
        return $this->updator;
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
     * @return Order
     */
    public function setContact(string $contact): self
    {
        $this->contact = $contact;

        return $this;
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

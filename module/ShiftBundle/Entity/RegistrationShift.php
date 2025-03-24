<?php

namespace ShiftBundle\Entity;

use CalendarBundle\Entity\Node\Event;
use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\General\Location;
use CommonBundle\Entity\General\Organization\Unit;
use CommonBundle\Entity\User\Person;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use ShiftBundle\Entity\Shift\Registered;

/**
 * This entity stores a shift.
 *
 * Flight Mode
 * This file was edited by Pieter Maene while in flight from Vienna to Brussels
 *
 * @ORM\Entity(repositoryClass="ShiftBundle\Repository\RegistrationShift")
 * @ORM\Table(name="shift_registration_shifts")
 */
class RegistrationShift
{
    /**
     * @var integer The ID of this shift
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Person The person who created this shift
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="creation_person", referencedColumnName="id")
     */
    private $creationPerson;

    /**
     * @var AcademicYear The shift's academic year
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @ORM\JoinColumn(name="academic_year", referencedColumnName="id")
     */
    private $academicYear;

    /**
     * @var DateTime The moment this shift starts
     *
     * @ORM\Column(name="start_date", type="datetime")
     */
    private $startDate;

    /**
     * @var DateTime The moment this shift ends
     *
     * @ORM\Column(name="end_date", type="datetime")
     */
    private $endDate;

    /**
     * @var DateTime The moment this shift starts
     *
     * @ORM\Column(name="visible_date", type="datetime", nullable=true)
     */
    private $visibleDate;

    /**
     * @var DateTime The moment this shift starts
     *
     * @ORM\Column(name="signout_date", type="datetime", nullable=true)
     */
    private $signoutDate;

    /**
     * @var DateTime The moment after which there can be no signins
     *
     * @ORM\Column(name="final_signin_date", type="datetime", nullable=true)
     */
    private $finalSigninDate;

    /**
     * @var integer The required number of volunteers for this shift
     *
     * @ORM\Column(name="nb_registered", type="integer")
     */
    private $nbRegistered;

    /**
     * @var ArrayCollection The people that volunteered for this shift
     *
     * @ORM\ManyToMany(targetEntity="ShiftBundle\Entity\Shift\Registered", cascade={"persist", "remove"})
     * @ORM\JoinTable(
     *      name="shift_registration_shifts_registered_map",
     *      joinColumns={@ORM\JoinColumn(name="registrations_shift", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="registered", referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"signupTime" = "ASC"})
     */
    private $registered;

    /**
     * @var Unit The organization unit this shift belongs to
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Organization\Unit")
     * @ORM\JoinColumn(name="unit", referencedColumnName="id")
     */
    private $unit;

    /**
     * @var Event The shift's event
     *
     * @ORM\ManyToOne(targetEntity="CalendarBundle\Entity\Node\Event")
     * @ORM\JoinColumn(name="event", referencedColumnName="id")
     */
    private $event;

    /**
     * @var Location The shift's location
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Location")
     * @ORM\JoinColumn(name="location", referencedColumnName="id", nullable=true)
     */
    private $location;

    /**
     * @var string The shift's name
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var string The shift's description
     *
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @var ArrayCollection The roles that can edit this shift
     *
     * @ORM\ManyToMany(targetEntity="CommonBundle\Entity\Acl\Role")
     * @ORM\JoinTable(
     *      name="shift_registration_shifts_roles_map",
     *      joinColumns={@ORM\JoinColumn(name="registration_shift", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role", referencedColumnName="name")}
     * )
     */
    private $editRoles;

    /**
     * @var boolean whether or not a ticket is needed to do the shift
     *
     * @ORM\Column(name="ticket_needed", type="boolean",options={"default" = false})
     */
    private $ticketNeeded;

    /**
     * @var boolean If this shift can only be used by members
     *
     * @ORM\Column(name="members_only",type="boolean",options={"default" = false})
     */
    private $membersOnly;

    /**
     * @var boolean If the members of this timeslot can be seen by members
     *
     * @ORM\Column(name="members_visible",type="boolean",options={"default" = false})
     */
    private $membersVisible;

    /**
     * @var boolean If this timeslot is a cudi timeslot
     *
     * @ORM\Column(name="is_cudi_timeslot",type="boolean",options={"default" = false})
     */
    private $cudiTimeslot;

    /**
     * @param Person       $creationPerson
     * @param AcademicYear $academicYear
     */
    public function __construct(Person $creationPerson, AcademicYear $academicYear)
    {
        $this->creationPerson = $creationPerson;
        $this->academicYear = $academicYear;

        $this->registered = new ArrayCollection();
        $this->editRoles = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Person
     */
    public function getCreationPerson()
    {
        return $this->creationPerson;
    }

    /**
     * @return AcademicYear
     */
    public function getAcademicYear()
    {
        return $this->academicYear;
    }

    /**
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param  DateTime $startDate
     * @return self
     */
    public function setStartDate(DateTime $startDate)
    {
        $this->startDate = $startDate;

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
     * @param  DateTime $endDate
     * @return self
     */
    public function setEndDate(DateTime $endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getFinalSigninDate()
    {
        return $this->finalSigninDate;
    }

    /**
     * @param DateTime $finalSigninDate
     */
    public function setFinalSigninDate(DateTime $finalSigninDate)
    {
        $this->finalSigninDate = $finalSigninDate;
    }

    /**
     * @return DateTime
     */
    public function getVisibleDate()
    {
        return $this->visibleDate;
    }

    /**
     * @param  DateTime $visibleDate
     * @return self
     */
    public function setVisibleDate(DateTime $visibleDate)
    {
        $this->visibleDate = $visibleDate;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getSignoutDate()
    {
        return $this->signoutDate;
    }

    /**
     * @param  DateTime $startDate
     * @return self
     */
    public function setSignoutDate(DateTime $signoutDate)
    {
        $this->signoutDate = $signoutDate;

        return $this;
    }

    /**
     * @return integer
     */
    public function getNbRegistered()
    {
        return $this->nbRegistered;
    }

    /**
     * @param  integer $nbRegistered
     * @return self
     */
    public function setNbRegistered($nbRegistered)
    {
        $this->nbRegistered = $nbRegistered;

        while ($this->countRegistered() > $nbRegistered) {
            $this->registered->remove($this->countRegistered() - 1);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getRegistered()
    {
        return $this->registered->toArray();
    }

    /**
     * @param  EntityManager $entityManager The EntityManager instance
     * @param  Registered    $registered
     * @return self
     */
    public function addRegistered(EntityManager $entityManager, Registered $registered)
    {
        if (!$this->canHaveAsRegistered($entityManager, $registered->getPerson())) {
            throw new InvalidArgumentException('The given person cannot register on this shift');
        }

        $this->registered->add($registered);

        return $this;
    }

    /**
     * @param  Person $person
     * @return self
     */
    public function removeRegistered(Person $person)
    {
        foreach ($this->registered as $registered) {
            if ($registered->getPerson() === $person) {
                $this->registered->removeElement($registered);

                return $registered;
            }
        }

        return null;
    }

    /**
     * @return integer
     */
    public function countRegistered()
    {
        return $this->registered->count();
    }

    /**
     * Checks whether or not the given person qualifies as a registered for this
     * shift.
     *
     * @param  EntityManager $entityManager The EntityManager instance
     * @param  Person        $registered    The person that should be checked
     * @return boolean
     */
    public function canHaveAsRegistered(EntityManager $entityManager, Person $registered)
    {
        $shifts = $entityManager->getRepository('ShiftBundle\Entity\RegistrationShift')
            ->findAllActiveByPerson($registered);//TODO: Create

        foreach ($shifts as $shift) {
            if ($shift === $this) {
                return false;
            }

            if (($this->getStartDate() < $shift->getEndDate() && $shift->getStartDate() < $this->getEndDate()) || $this->getStartDate() === $shift->getStartDate()) {
                return false;
            }

            if ($this->getStartDate()->format('Y-m-d') === $shift->getStartDate()->format('Y-m-d')) {
                return false;
            }
        }

        if ($this->getFinalSigninDate() !== null && $this->getFinalSigninDate() < new DateTime()) {
            return false;
        }

        return !($this->countRegistered() >= $this->getNbRegistered());
    }

    /**
     * Checks whether or not the given person already has a registered shift on the same day as this shift
     * shift.
     *
     * @param  EntityManager $entityManager The EntityManager instance
     * @param  Person        $registered    The person that should be checked
     * @return boolean
     */
    public function hasShiftOnThisDay(EntityManager $entityManager, Person $registered)
    {
        $shifts = $entityManager->getRepository('ShiftBundle\Entity\RegistrationShift')
            ->findAllActiveByPerson($registered);//TODO: Create
        foreach ($shifts as $shift) {
            if ($this->getStartDate()->format('Y-m-d') === $shift->getStartDate()->format('Y-m-d')) {
                return true;
            }
        }
        return false;
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
     * @return self
     */
    public function setUnit(Unit $unit)
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param  Event|null $event
     * @return self
     */
    public function setEvent(Event $event = null)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param  Location $location
     * @return self
     */
    public function setLocation(Location $location)
    {
        $this->location = $location;

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
     * @param  string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

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
     * @param  string $description
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return array
     */
    public function getEditRoles()
    {
        return $this->editRoles->toArray();
    }

    /**
     * @param  array $editRoles
     * @return self
     */
    public function setEditRoles(array $editRoles)
    {
        $this->editRoles = new ArrayCollection($editRoles);

        return $this;
    }

    /**
     * Whether or not this shift's dates can be edited.
     *
     * @return boolean
     */
    public function canEditDates()
    {
        return $this->countRegistered() == 0;
    }

    /**
     * Check whether or not the given person can sign out from this shift.
     *
     * @return boolean
     */
    public function canSignOut()
    {
        $now = new DateTime();
        $signoutDate = $this->signoutDate;
        if ($signoutDate !== null) {
            return !($this->signoutDate < $now);
        }
        return true;
    }

    /**
     * Preparing the removal of this shift. Basically a small hack because the
     * cascade options aren't doing what they're supposed to do.
     *
     * @return self
     */
    public function prepareRemove()
    {
        $this->registered = new ArrayCollection();
        return $this;
    }

    /**
     * Indicates whether the given person can edit this shift and its subscriptions.
     *
     * @param  Person|null $person The person to check.
     * @return boolean
     */
    public function canBeEditedBy(Person $person = null)
    {
        if ($person == null) {
            return false;
        }

        if ($this->getCreationPerson()->getId() === $person->getId()) {
            return true;
        }

        foreach ($person->getFlattenedRoles() as $role) {
            if ($this->editRoles->contains($role) || $role->getName() == 'editor') {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  boolean $ticketNeeded
     * @return self
     */
    public function setTicketNeeded($ticketNeeded)
    {
        $this->ticketNeeded = $ticketNeeded;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isTicketNeeded()
    {
        return $this->ticketNeeded;
    }

    /**
     * @param  boolean $membersOnly
     * @return self
     */
    public function setMembersOnly($membersOnly)
    {
        $this->membersOnly = $membersOnly;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isMembersOnly()
    {
        return $this->membersOnly;
    }

    /**
     * @param  boolean cudiTimeSlot
     * @return self
     */
    public function setCudiTimeslot($cudiTimeslot)
    {
        $this->cudiTimeslot = $cudiTimeslot;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isCudiTimeslot()
    {
        return $this->cudiTimeslot;
    }

    /**
     * @return boolean
     */
    public function isMembersVisible(): bool
    {
        return $this->membersVisible;
    }

    /**
     * @param boolean $membersVisible
     */
    public function setMembersVisible(bool $membersVisible)
    {
        $this->membersVisible = $membersVisible;
    }
}

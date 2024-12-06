<?php

namespace ShiftBundle\Entity;

use CalendarBundle\Entity\Node\Event;
use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\General\Location;
use CommonBundle\Entity\General\Organization\Unit;
use CommonBundle\Entity\User\Person;
use DateInterval;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use ShiftBundle\Entity\Shift\Responsible;
use ShiftBundle\Entity\Shift\Volunteer;

/**
 * This entity stores a shift.
 *
 * Flight Mode
 * This file was edited by Pieter Maene while in flight from Vienna to Brussels
 *
 * @ORM\Entity(repositoryClass="ShiftBundle\Repository\Shift")
 * @ORM\Table(name="shift_shifts")
 */
class Shift
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
     * @var Person The person that manages this shift
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="manager", referencedColumnName="id")
     */
    private $manager;

    /**
     * @var integer The required number of responsibles for this shift
     *
     * @ORM\Column(name="nb_responsibles", type="integer")
     */
    private $nbResponsibles;

    /**
     * @var ArrayCollection The people that are responsible for this shift
     *
     * @ORM\ManyToMany(targetEntity="ShiftBundle\Entity\Shift\Responsible", cascade={"persist", "remove"})
     * @ORM\JoinTable(
     *      name="shift_shifts_responsibles_map",
     *      joinColumns={@ORM\JoinColumn(name="shift", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="responsible", referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"signupTime" = "ASC"})
     */
    private $responsibles;

    /**
     * @var integer The maximum required number of volunteers for this shift
     *
     * @ORM\Column(name="nb_volunteers", type="integer")
     */
    private $nbVolunteers;

    /**
     * @var integer The minimum required number of volunteers for this shift
     *
     * @ORM\Column(name="nb_volunteers_min", type="integer", nullable=true)
     */
    private $nbVolunteersMin;

    /**
     * @var ArrayCollection The people that volunteered for this shift
     *
     * @ORM\ManyToMany(targetEntity="ShiftBundle\Entity\Shift\Volunteer", cascade={"persist", "remove"})
     * @ORM\JoinTable(
     *      name="shift_shifts_volunteers_map",
     *      joinColumns={@ORM\JoinColumn(name="shift", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="volunteer", referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"signupTime" = "ASC"})
     */
    private $volunteers;

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
     * @ORM\JoinColumn(name="location", referencedColumnName="id")
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
     *      name="shift_shifts_roles_map",
     *      joinColumns={@ORM\JoinColumn(name="shift", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role", referencedColumnName="name")}
     * )
     */
    private $editRoles;

    /**
     * @var integer The amount of coins this shift is worth. These coins can be payed out to volunteers.
     *
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    private $reward;

    /**
     * @var integer The amount of points linked to this shift. Points are non-payable fictive rewards used in the ranking.
     *
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    private $points;

    /**
     * @var boolean Whether the reward is payed at the event itself
     *
     * @ORM\Column(name="handled_on_event", type="boolean")
     */
    private $handledOnEvent;

    /**
     * @var boolean whether a ticket is needed to do the shift
     *
     * @ORM\Column(name="ticket_needed", type="boolean",options={"default" = false})
     */
    private $ticketNeeded;

    /**
     * @param Person       $creationPerson
     * @param AcademicYear $academicYear
     */
    public function __construct(Person $creationPerson, AcademicYear $academicYear)
    {
        $this->creationPerson = $creationPerson;
        $this->academicYear = $academicYear;

        $this->responsibles = new ArrayCollection();
        $this->volunteers = new ArrayCollection();
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
     * @return Person
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @param  Person $manager
     * @return self
     */
    public function setManager(Person $manager)
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * @return integer
     */
    public function getNbResponsibles()
    {
        return $this->nbResponsibles;
    }

    /**
     * @param  integer $nbResponsibles
     * @return self
     */
    public function setNbResponsibles($nbResponsibles)
    {
        $this->nbResponsibles = $nbResponsibles;

        while ($this->countResponsibles() > $nbResponsibles) {
            $this->responsibles->remove($this->countResponsibles() - 1);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getResponsibles()
    {
        return $this->responsibles->toArray();
    }

    /**
     * @param  EntityManager $entityManager The EntityManager instance
     * @param  Responsible   $responsible
     * @return self
     */
    public function addResponsible(EntityManager $entityManager, Responsible $responsible)
    {
        if (!$this->canHaveAsResponsible($entityManager, $responsible->getPerson())) {
            throw new InvalidArgumentException('The given responsible cannot be added to this shift');
        }

        $this->responsibles->add($responsible);

        return $this;
    }

    /**
     * @param  Responsible $responsible
     * @return self
     */
    public function removeResponsible(Responsible $responsible)
    {
        $this->responsibles->removeElement($responsible);

        return $this;
    }

    /**
     * @return integer
     */
    public function countResponsibles()
    {
        return $this->responsibles->count();
    }

    /**
     * Checks whether or not the given person qualifies as a responsible for this
     * shift.
     *
     * @param  EntityManager $entityManager The EntityManager instance
     * @param  Person        $person        The person that should be checked
     * @return boolean
     */
    public function canHaveAsResponsible(EntityManager $entityManager, Person $person)
    {
        if (!$person->isPraesidium($this->getAcademicYear())) {
            return false;
        }

        $shifts = $entityManager->getRepository('ShiftBundle\Entity\Shift')
            ->findAllActiveByPerson($person);

        foreach ($shifts as $shift) {
            if ($shift === $this) {
                return false;
            }

            if ($this->getStartDate() < $shift->getEndDate() && $shift->getStartDate() < $this->getEndDate()) {
                return false;
            }
        }

        return !($this->countResponsibles() >= $this->getNbResponsibles());
    }

    /**
     * @return integer
     */
    public function getNbVolunteers()
    {
        return $this->nbVolunteers;
    }

    /**
     * @param  integer $nbVolunteers
     * @return self
     */
    public function setNbVolunteers($nbVolunteers)
    {
        $this->nbVolunteers = $nbVolunteers;

        while ($this->countVolunteers() > $nbVolunteers) {
            $this->volunteers->remove($this->countVolunteers() - 1);
        }

        return $this;
    }

    /**
     * @return integer
     */
    public function getNbVolunteersMin()
    {
        return $this->nbVolunteersMin ?: $this->nbVolunteers; // return nbVolunteersMin or nbVolunteers if null
    }

    /**
     * @param  integer $nbVolunteersMin
     * @return self
     */
    public function setNbVolunteersMin($nbVolunteersMin)
    {
        $this->nbVolunteersMin = min($nbVolunteersMin, $this->nbVolunteers);

        return $this;
    }

    /**
     * @return array
     */
    public function getVolunteers()
    {
        return $this->volunteers->toArray();
    }

    /**
     * @param  EntityManager $entityManager The EntityManager instance
     * @param  Volunteer     $volunteer
     * @return self
     */
    public function addVolunteer(EntityManager $entityManager, Volunteer $volunteer)
    {
        if (!$this->canHaveAsVolunteer($entityManager, $volunteer->getPerson())) {
            throw new InvalidArgumentException('The given volunteer cannot be added to this shift');
        }

        $this->volunteers->add($volunteer);

        return $this;
    }

    /**
     * @param  Volunteer $volunteer
     * @return self
     */
    public function removeVolunteer(Volunteer $volunteer)
    {
        $this->volunteers->removeElement($volunteer);

        return $this;
    }

    /**
     * @return integer
     */
    public function countVolunteers()
    {
        return $this->volunteers->count();
    }

    /**
     * Checks whether or not the given person qualifies as a volunteer for this
     * shift.
     *
     * @param  EntityManager $entityManager The EntityManager instance
     * @param  Person        $person        The person that should be checked
     * @return boolean
     */
    public function canHaveAsVolunteer(EntityManager $entityManager, Person $person)
    {
        $shifts = $entityManager->getRepository('ShiftBundle\Entity\Shift')
            ->findAllActiveByPerson($person);

        foreach ($shifts as $shift) {
            if ($shift === $this) {
                return false;
            }

            if ($this->getStartDate() < $shift->getEndDate() && $shift->getStartDate() < $this->getEndDate() || $shift->getStartDate() === $this->getStartDate()) {
                return false;
            }
        }

        if ($this->countVolunteers() >= $this->getNbVolunteers()) {
            foreach ($this->volunteers as $volunteer) {
                $now = new DateTime();

                $responsibleSignoutTreshold = new DateInterval(
                    $entityManager->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('shift.responsible_signout_treshold')
                );

                $getStartDate = clone $this->getStartDate();

                if ($volunteer->getPerson()->isPraesidium($this->getAcademicYear())) {
                    if (!$person->isPraesidium($this->getAcademicYear()) && $getStartDate->sub($responsibleSignoutTreshold) > $now) {
                        return true;
                    }
                }
            }

            return false;
        }

        return true;
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
        return $this->countResponsibles() == 0 && $this->countVolunteers() == 0;
    }

    /**
     * Check whether or not the given person can sign out from this shift.
     *
     * @param  EntityManager $entityManager The EntityManager instance
     * @return boolean
     */
    public function canSignOut(EntityManager $entityManager)
    {
        $now = new DateTime();

        $signoutTreshold = new DateInterval(
            $entityManager->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('shift.signout_treshold')
        );

        $getStartDate = clone $this->getStartDate();

        return !($getStartDate->sub($signoutTreshold) < $now);
    }

    /**
     * Preparing the removal of this shift. Basically a small hack because the
     * cascade options aren't doing what they're supposed to do.
     *
     * @return self
     */
    public function prepareRemove()
    {
        $this->responsibles = new ArrayCollection();
        $this->volunteers = new ArrayCollection();

        return $this;
    }

    /**
     * Removes the given person from this shift.
     *
     * @param  Person $person The person that should be removed
     * @return Responsible|Volunteer
     */
    public function removePerson(Person $person)
    {
        foreach ($this->volunteers as $volunteer) {
            if ($volunteer->getPerson() === $person) {
                $this->removeVolunteer($volunteer);

                return $volunteer;
            }
        }

        foreach ($this->responsibles as $responsible) {
            if ($responsible->getPerson() === $person) {
                $this->removeResponsible($responsible);

                return $responsible;
            }
        }

        return null;
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
     * @param  integer $reward
     * @return self
     */
    public function setReward($reward)
    {
        $this->reward = $reward;

        return $this;
    }

    /**
     * @return integer
     */
    public function getReward()
    {
        return $this->reward;
    }

    /**
     * @param  integer $points
     * @return self
     */
    public function setPoints($points)
    {
        $this->points = $points;

        return $this;
    }

    /**
     * @return integer
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * @param  boolean $handledOnEvent
     * @return self
     */
    public function setHandledOnEvent($handledOnEvent)
    {
        $this->handledOnEvent = $handledOnEvent;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getHandledOnEvent()
    {
        return $this->handledOnEvent;
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
    public function getTicketNeeded()
    {
        return $this->ticketNeeded;
    }
}

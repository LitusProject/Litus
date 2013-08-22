<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace ShiftBundle\Entity;

use DateInterval,
    DateTime,
    CalendarBundle\Entity\Node\Event,
    CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\General\Location,
    CommonBundle\Entity\General\Organization\Unit,
    CommonBundle\Entity\User\Person,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM,
    ShiftBundle\Entity\Shift\Responsible,
    ShiftBundle\Entity\Shift\Volunteer;

/**
 * This entity stores a shift.
 *
 * Flight Mode
 * This file was edited by Pieter Maene while in flight from Vienna to Brussels
 *
 * @ORM\Entity(repositoryClass="ShiftBundle\Repository\Shift")
 * @ORM\Table(name="shifts.shifts")
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
     * @var \CommonBundle\Entity\User\Person The person who created this shift
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="creation_person", referencedColumnName="id")
     */
    private $creationPerson;

    /**
     * @var \CommonBundle\Entity\General\AcademicYear The shift's academic year
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @ORM\JoinColumn(name="academic_year", referencedColumnName="id")
     */
    private $academicYear;

    /**
     * @var boolean The moment this shift starts
     *
     * @ORM\Column(name="start_date", type="datetime")
     */
    private $startDate;

    /**
     * @var string The moment this shift ends
     *
     * @ORM\Column(name="end_date", type="datetime")
     */
    private $endDate;

    /**
     * @var \CommonBundle\Entity\User\Person The person that manages this shift
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
     * @var \Doctrine\Common\Collections\ArrayCollection The people that are responsible for this shift
     *
     * @ORM\ManyToMany(targetEntity="ShiftBundle\Entity\Shift\Responsible", cascade={"persist", "remove"})
     * @ORM\JoinTable(
     *      name="shifts.shifts_responsibles_map",
     *      joinColumns={@ORM\JoinColumn(name="shift", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="responsible", referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"signupTime" = "ASC"})
     */
    private $responsibles;

    /**
     * @var integer The required number of volunteers for this shift
     *
     * @ORM\Column(name="nb_volunteers", type="integer")
     */
    private $nbVolunteers;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection The people that volunteered for this shift
     *
     * @ORM\ManyToMany(targetEntity="ShiftBundle\Entity\Shift\Volunteer", cascade={"persist", "remove"})
     * @ORM\JoinTable(
     *      name="shifts.shifts_volunteers_map",
     *      joinColumns={@ORM\JoinColumn(name="shift", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="volunteer", referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"signupTime" = "ASC"})
     */
    private $volunteers;

    /**
     * @var \CommonBundle\Entity\General\Organization\Unit The organization unit this shift belongs to
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Organization\Unit")
     * @ORM\JoinColumn(name="unit", referencedColumnName="id")
     */
    private $unit;

    /**
     * @var \CalendarBundle\Entity\Node\Event The shift's event
     *
     * @ORM\ManyToOne(targetEntity="CalendarBundle\Entity\Node\Event")
     * @ORM\JoinColumn(name="event", referencedColumnName="id")
     */
    private $event;

    /**
     * @var \CommonBundle\Entity\General\Location The shift's location
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
     * @param \CommonBundle\Entity\User\Person $creationPerson
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param \CommonBundle\Entity\User\Person $manager
     * @param integer $nbResponsibles
     * @param integer $nbVolunteers
     * @param \CommonBundle\Entity\General\Organization\Unit $unit
     * @param \CommonBundle\Entity\General\Location $location
     * @param string $name
     * @param string $description
     */
    public function __construct(
        Person $creationPerson, AcademicYear $academicYear, DateTime $startDate, DateTime $endDate, Person $manager, $nbResponsibles, $nbVolunteers, Unit $unit, Location $location, $name, $description
    )
    {
        $this->creationPerson = $creationPerson;
        $this->academicYear = $academicYear;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->manager = $manager;
        $this->nbResponsibles = $nbResponsibles;
        $this->nbVolunteers = $nbVolunteers;
        $this->unit = $unit;
        $this->location = $location;
        $this->name = $name;
        $this->description = $description;

        $this->responsibles = new ArrayCollection();
        $this->volunteers = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \CommonBundle\Entity\User\Person
     */
    public function getCreationPerson()
    {
        return $this->creationPerson;
    }

    /**
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    public function getAcademicYear()
    {
        return $this->academicYear;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $startDate
     * @return \ShiftBundle\Entity\Shift
     */
    public function setStartDate(DateTime $startDate)
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param \DateTime $endDate
     * @return \ShiftBundle\Entity\Shift
     */
    public function setEndDate(DateTime $endDate)
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @return \CommonBundle\Entity\User\Person
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @param \CommonBundle\Entity\User\Person $manager
     * @return \ShiftBundle\Entity\Shift
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
     * @param integer $nbResponsibles
     * @return \ShiftBundle\Entity\Shift
     */
    public function setNbResponsibles($nbResponsibles)
    {
        $this->nbResponsibles = $nbResponsibles;
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
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \ShiftBundle\Entity\Shift\Responsible $responsible
     * @return \ShiftBundle\Entity\Shift
     */
    public function addResponsible(EntityManager $entityManager, Responsible $responsible)
    {
        if (!$this->canHaveAsResponsible($entityManager, $responsible->getPerson()))
            throw new \InvalidArgumentException('The given responsible cannot be added to this shift');

        $this->responsibles->add($responsible);
        return $this;
    }

    /**
     * @param \ShiftBundle\Entity\Shift\Responsible $responsible
     * @return \ShiftBundle\Entity\Shift
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
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \CommonBundle\Entity\User\Person $person The person that should be checked
     * @return boolean
     */
    public function canHaveAsResponsible(EntityManager $entityManager, Person $person)
    {
        if (!$person->isPraesidium($this->getAcademicYear()))
            return false;

        $shifts = $entityManager->getRepository('ShiftBundle\Entity\Shift')
            ->findAllActiveByPerson($person);

        foreach ($shifts as $shift) {
            if ($shift === $this)
                return false;

            if ($this->getStartDate() < $shift->getEndDate() && $shift->getStartDate() < $this->getEndDate())
                return false;
        }

        if ($this->countResponsibles() >= $this->getNbResponsibles())
            return false;

        return true;
    }

    /**
     * @return integer
     */
    public function getNbVolunteers()
    {
        return $this->nbVolunteers;
    }

    /**
     * @param integer $nbVolunteers
     * @return \ShiftBundle\Entity\Shift
     */
    public function setNbVolunteers($nbVolunteers)
    {
        $this->nbVolunteers = $nbVolunteers;
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
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \ShiftBundle\Entity\Shift\Volunteer $volunteer
     * @return \ShiftBundle\Entity\Shift
     */
    public function addVolunteer(EntityManager $entityManager, Volunteer $volunteer)
    {
        if (!$this->canHaveAsVolunteer($entityManager, $volunteer->getPerson()))
            throw new \InvalidArgumentException('The given volunteer cannot be added to this shift');

        $this->volunteers->add($volunteer);
        return $this;
    }

    /**
     * @param \ShiftBundle\Entity\Shift\Volunteer $volunteer
     * @return \ShiftBundle\Entity\Shift
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
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \CommonBundle\Entity\User\Person $person The person that should be checked
     * @return boolean
     */
    public function canHaveAsVolunteer(EntityManager $entityManager, Person $person)
    {
        $shifts = $entityManager->getRepository('ShiftBundle\Entity\Shift')
            ->findAllActiveByPerson($person);

        foreach ($shifts as $shift) {
            if ($shift === $this)
                return false;

            if ($this->getStartDate() < $shift->getEndDate() && $shift->getStartDate() < $this->getEndDate())
                return false;
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
                    if (!$person->isPraesidium($this->getAcademicYear()) && $getStartDate->sub($responsibleSignoutTreshold) > $now)
                        return true;
                }
            }

            return false;
        }

        return true;
    }

    /**
     * @return \CommonBundle\Entity\General\Organization\Unit
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param \CommonBundle\Entity\General\Organization\Unit $unit
     * @return \ShiftBundle\Entity\Shift
     */
    public function setUnit(Unit $unit)
    {
        $this->unit = $unit;
        return $this;
    }

    /**
     * @return \CommonBundle\Entity\General\Organization\Unit
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param \CalendarBundle\Entity\Node\Event $event
     * @return \ShiftBundle\Entity\Shift
     */
    public function setEvent($event)
    {
        $this->event = $event;
        return $this;
    }

    /**
     * @return \CommonBundle\Entity\General\Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param \CommonBundle\Entity\General\Location $location
     * @return \ShiftBundle\Entity\Shift
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
     * @param string $name
     * @return \ShiftBundle\Entity\Shift
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
     * @param string $description
     * @return \ShiftBundle\Entity\Shift
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Whether or not this shift's dates can be edited.
     *
     * @return boolean
     */
    public function canEditDates()
    {
        return (0 == $this->countResponsibles()) && (0 == $this->countVolunteers());
    }

    /**
     * Check whether or not the given person can sign out from this shift.
     *
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \CommonBundle\Entity\User\Person $person The person that should be checked
     * @return boolean
     */
    public function canSignout(EntityManager $entityManager, Person $person)
    {
        $now = new DateTime();

        $signoutTreshold = new DateInterval(
            $entityManager->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('shift.signout_treshold')
        );

        $getStartDate = clone $this->getStartDate();

        if ($getStartDate->sub($signoutTreshold) < $now)
             return false;

        return true;
    }

    /**
     * Preparing the removal of this shift. Basically a small hack because the
     * cascade options aren't doing what they're supposed to do.
     *
     * @return \ShiftBundle\Entity\Shift
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
     * @param \CommonBundle\Entity\User\Person $person The person that should be removed
     * @return \ShiftBundle\Entity\Shift\Responsible|\ShiftBundle\Entity\Shift\Volunteer
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
     * @param \CommonBundle\Entity\User\Person $person The person to check.
     * @return boolean
     */
    public function canBeEditedBy(Person $person = null)
    {
        if (null == $person)
            return false;

        if ($this->getCreationPerson()->getId() === $person->getId())
            return true;

        foreach ($person->getFlattenedRoles() as $role) {
            if ($role->getName() == 'editor')
                return true;
        }

        return false;
    }
}

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

namespace ShiftBundle\Entity;

use DateTime,
    CalendarBundle\Entity\Nodes\Event,
    CommonBundle\Entity\General\Location,
    CommonBundle\Entity\Users\Person,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM,
    ShiftBundle\Entity\Unit;

/**
 * This entity stores a shift.
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
     * @var \CommonBundle\Entity\Users\Person The person who created this shift
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\Users\Person")
     * @ORM\JoinColumn(name="creation_person", referencedColumnName="id")
     */
    private $creationPerson;

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
     * @var \CommonBundle\Entity\Users\Person The person that manages this shift
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\Users\Person")
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
     * @ORM\ManyToMany(targetEntity="CommonBundle\Entity\Users\Person")
     * @ORM\JoinTable(
     *      name="shifts.shifts_responsibles_map",
     *      joinColumns={@ORM\JoinColumn(name="shift", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="volunteer", referencedColumnName="id")}
     * )
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
     * @ORM\ManyToMany(targetEntity="CommonBundle\Entity\Users\Person")
     * @ORM\JoinTable(
     *      name="shifts.shifts_volunteers_map",
     *      joinColumns={@ORM\JoinColumn(name="shift", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="volunteer", referencedColumnName="id")}
     * )
     */
    private $volunteers;

    /**
     * @var \ShiftBundle\Entity\Unit The organization unit this shift belongs to
     *
     * @ORM\ManyToOne(targetEntity="ShiftBundle\Entity\Unit")
     * @ORM\JoinColumn(name="unit", referencedColumnName="id")
     */
    private $unit;

    /**
     * @var \CalendarBundle\Entity\Nodes\Event The shift's event
     *
     * @ORM\ManyToOne(targetEntity="CalendarBundle\Entity\Nodes\Event")
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
     * @param \CommonBundle\Entity\Users\Person $creationPerson
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param \CommonBundle\Entity\Users\Person $manager
     * @param integer $nbResponsibles
     * @param integer $nbVolunteers
     * @param \ShiftBundle\Entity\Unit $unit
     * @param \CalendarBundle\Entity\Nodes\Event $event
     * @param \CommonBundle\Entity\General\Location $location
     * @param string $name
     * @param string $description
     */
    public function __construct(
        Person $creationPerson, DateTime $startDate, DateTime $endDate, Person $manager, $nbResponsibles, $nbVolunteers, Unit $unit, Location $location, $name, $description
    )
    {
        $this->creationPerson = $creationPerson;
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
     * @return \CommonBundle\Entity\Users\Person
     */
    public function getCreationPerson()
    {
        return $this->creationPerson;
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
     * @return \CommonBundle\Entity\Users\Person
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @param \CommonBundle\Entity\Users\Person $manager
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
     * @return \ShiftBundle\Entity\Unit
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param \ShiftBundle\Entity\Unit $unit
     * @return \ShiftBundle\Entity\Shift
     */
    public function setUnit(Unit $unit)
    {
        $this->unit = $unit;
        return $this;
    }

    /**
     * @return \ShiftBundle\Entity\Unit
     */
    public function getEvent()
    {
        return $this->unit;
    }

    /**
     * @param \CalendarBundle\Entity\Nodes\Event $event
     * @return \ShiftBundle\Entity\Shift
     */
    public function setEvent(Event $event)
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
}

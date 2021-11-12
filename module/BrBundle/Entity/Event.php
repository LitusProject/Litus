<?php

namespace BrBundle\Entity;

use CommonBundle\Entity\User\Person;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for an event, organised by VTK Corporate Relations
 *
 * @author Matthias Swiggers <matthias.swiggers@vtk.be>
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Event")
 * @ORM\Table(name="br_events")
 */
class Event
{
    /**
     * @var integer The event's unique identifier
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Person The creator of this event
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="creator", referencedColumnName="id")
     */
    private $creator;

    /**
     * @var string Title of the event
     *
     * @ORM\Column(name="title", type="text")
     *
     */
    private $title;

    /**
     * @var string The description for this event for students
     *
     * @ORM\Column(name="description_for_students", type="text", nullable=true)
     */
    private $descriptionForStudents;

    /**
     * @var string The description for this event for companies
     *
     * @ORM\Column(name="description_for_companies", type="text", nullable=true)
     */
    private $descriptionForCompanies;

    /**
     * @var DateTime The start date and time of this event.
     *
     * @ORM\Column(name="start_date", type="datetime")
     */
    private $startDate;

    /**
     * @var DateTime The end date and time of this event.
     *
     * @ORM\Column(name="end_date", type="datetime")
     */
    private $endDate;

    /**
     * @var DateTime The start date and time of the subscriptions.
     *
     * @ORM\Column(name="subscription_date", type="datetime", nullable=true)
     */
    private $subscriptionDate;

    /**
     * @var DateTime The start date and time of the map view. After this time the map is displayed
     *
     * @ORM\Column(name="mapview_date", type="datetime", nullable=true)
     */
    private $mapviewDate;

    /**
     * @var integer The number of companies that will attend
     *
     * @ORM\Column(name="nb_companies", type="integer", nullable=true)
     */
    private $nbCompanies;

    /**
     * @var integer The number of students that will attend
     *
     * @ORM\Column(name="nb_students", type="integer", nullable=true)
     */
    private $nbStudents;

    /**
     * @var boolean The flag whether the Event is visible for Companies
     *
     * @ORM\Column(name="visible_for_companies", type="boolean", nullable=true)
     */
    private $visibleForCompanies;

    /**
     * @var boolean The flag whether the Event is visible for Students
     *
     * @ORM\Column(name="visible_for_students", type="boolean", nullable=true)
     */
    private $visibleForStudents;

    /**
     * @var string Location of the event
     *
     * @ORM\Column(name="location", type="text", nullable=true)
     *
     */
    private $location;

    /**
     * @var string Audience of the event
     *
     * @ORM\Column(name="audience", type="text", nullable=true)
     *
     */
    private $audience;

    /**
     * @param Person $creator
     */
    public function __construct(Person $creator)
    {
        $this->creator = $creator;
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
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * @param  string $title
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
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
    public function getStartDate()
    {
        return $this->startDate;
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
     * @return DateTime
     */
    public function getSubscriptionDate()
    {
        return $this->subscriptionDate;
    }

    /**
     * @param  DateTime $endDate
     * @return self
     */
    public function setSubscriptionDate(DateTime $subscriptionDate)
    {
        $this->subscriptionDate = $subscriptionDate;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getMapviewDate()
    {
        return $this->mapviewDate;
    }

    /**
     * @param  DateTime $mapviewDate
     * @return self
     */
    public function setMapviewDate(DateTime $mapviewDate)
    {
        $this->mapviewDate = $mapviewDate;

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
     * @return string
     */
    public function getDescriptionForStudents()
    {
        return $this->descriptionForStudents;
    }

    /**
     * @param string $descriptionForStudents
     */
    public function setDescriptionForStudents(string $descriptionForStudents)
    {
        $this->descriptionForStudents = $descriptionForStudents;
    }

    /**
     * @return string
     */
    public function getDescriptionForCompanies()
    {
        return $this->descriptionForCompanies;
    }

    /**
     * @param string $descriptionForCompanies
     */
    public function setDescriptionForCompanies(string $descriptionForCompanies)
    {
        $this->descriptionForCompanies = $descriptionForCompanies;
    }

    /**
     * @return integer
     */
    public function getNbCompanies()
    {
        return $this->nbCompanies;
    }

    /**
     * @param integer $nbCompanies
     */
    public function setNbCompanies(int $nbCompanies)
    {
        $this->nbCompanies = $nbCompanies;
    }

    /**
     * @return integer
     */
    public function getNbStudents()
    {
        return $this->nbStudents;
    }

    /**
     * @param integer $nbStudents
     */
    public function setNbStudents(int $nbStudents)
    {
        $this->nbStudents = $nbStudents;
    }

    /**
     * @return boolean
     */
    public function isVisibleForCompanies()
    {
        return $this->visibleForCompanies;
    }

    /**
     * @param boolean $visibleForCompanies
     */
    public function setVisibleForCompanies(bool $visibleForCompanies)
    {
        $this->visibleForCompanies = $visibleForCompanies;
    }

    /**
     * @return boolean
     */
    public function isVisibleForStudents()
    {
        return $this->visibleForStudents;
    }

    /**
     * @param boolean $visibleForStudents
     */
    public function setVisibleForStudents(bool $visibleForStudents)
    {
        $this->visibleForStudents = $visibleForStudents;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation(string $location)
    {
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function getAudience()
    {
        return $this->audience;
    }

    /**
     * @param string $audience
     */
    public function setAudience(string $audience)
    {
        $this->audience = $audience;
    }

    /**
     * @return string
     */
    public function setCompanies($companies)
    {

        return $this;
    }
}

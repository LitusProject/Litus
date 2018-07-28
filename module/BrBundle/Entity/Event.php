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

namespace BrBundle\Entity;

use CommonBundle\Entity\User\Person,
    DateTime,
    Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for an event, organised by VTK Corporate Relations
 *
 * @author Matthias Swiggers <matthias.swiggers@vtk.be>
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Event")
 * @ORM\Table(name="br.events")
 */
class Event
{
    /**
     * @var int The event's unique identifier
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
     * @var string The description for this event
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var DateTime The start date and time of this reservation.
     *
     * @ORM\Column(name="start_date", type="datetime")
     */
    private $startDate;

    /**
     * @var DateTime The end date and time of this reservation.
     *
     * @ORM\Column(name="end_date", type="datetime")
     */
    private $endDate;

    /**
     * @param ReservableResource $resource
     * @param Person             $creator
     */
    public function __construct(Person $creator)
    {
        $this->creator = $creator;
    }

    /**
     * @return int
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
    public function getEndDate()
    {
        return $this->endDate;
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
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
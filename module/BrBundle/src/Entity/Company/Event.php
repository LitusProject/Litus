<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Entity\Company;

use BrBundle\Entity\Company,
    DateTime;

/**
 * This is the entity for an event.
 *
 * @Entity(repositoryClass="BrBundle\Repository\Company\Event")
 * @Table(name="br.companies_events")
 */
class Event
{
    /**
     * @var string The event's ID
     *
     * @Id
     * @Column(type="bigint")
     * @GeneratedValue
     */
    private $id;

    /**
     * @var string The event's name
     *
     * @Column(type="string", length=50)
     */
    private $name;

    /**
     * @var string The event's location
     *
     * @Column(type="string")
     */
    private $location;

    /**
     * @var string The event's start date
     *
     * @Column(name="start_date", type="datetime")
     */
    private $startDate;

    /**
     * @var string The event's end date
     *
     * @Column(name="end_date", type="datetime")
     */
    private $endDate;

    /**
     * @var string The description of the event
     *
     * @Column(type="text")
     */
    private $description;

    /**
     * @var \BrBundle\Entity\Company The company of the event
     *
     * @OneToOne(targetEntity="BrBundle\Entity\Company")
     * @JoinColumn(name="company", referencedColumnName="id")
     */
    private $company;

    /**
     * @param string $name The event's name
     * @param string $location The company's location
     * @param \DateTime $startDate The event's start date
     * @param \DateTime $endDate The event's end date
     * @param string $description The event's description
     * @param \BrBundle\Entity\Company $company The event's company
     */
    public function __construct($name, $location, DateTime $startDate, DateTime $endDate, $description, Company $company)
    {
        $this->setName($name);
        $this->setLocation($location);
        $this->setStartDate($startDate);
        $this->setEndDate($endDate);
        $this->setDescription($description);

        $this->company = $company;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     * @return \BrBundle\Entity\Company\Event
     */
    public function setName($name)
    {
        if ((null === $name) || !is_string($name))
            throw new \InvalidArgumentException('Invalid name');

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
     * @param string $location
     * @return \BrBundle\Entity\Company\Event
     */
    public function setLocation($location)
    {
        if ((null === $location) || !is_string($location))
            throw new \InvalidArgumentException('Invalid location');

        $this->location = $location;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param \DateTime $startDate
     * @return \BrBundle\Entity\Company\Event
     */
    public function setStartDate(DateTime $startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $endDate
     * @return \BrBundle\Entity\Company\Event
     */
    public function setEndDate(DateTime $endDate)
    {
        $this->endDate = $endDate;

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
     * @param string $description
     * @return \BrBundle\Entity\Company\Event
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
     * @return \BrBundle\Entity\Company
     */
    public function getCompany()
    {
        return $this->company;
    }
}

<?php

namespace CalendarBundle\Entity\Nodes;

use CommonBundle\Entity\General\Language,
    CommonBundle\Entity\Users\Person;

/**
 * This entity stores the node item.
 *
 * @Entity(repositoryClass="CalendarBundle\Repository\Nodes\Event")
 * @Table(name="nodes.events")
 */
class Event extends \CommonBundle\Entity\Nodes\Node
{
    /**
     * @var array The translations of this event
     *
     * @OneToMany(targetEntity="CalendarBundle\Entity\Nodes\Translation", mappedBy="event", cascade={"remove"})
     */
    private $translations;

    /**
     * @var \DateTime The start date of this event
     *
     * @Column(type="datetime")
     */
    private $startDate;

    /**
     * @var \DateTime The end date of this event
     *
     * @Column(type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * @param \CommonBundle\Entity\Users\Person $person
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     */
    public function __construct(Person $person, $startDate, $endDate)
    {
        parent::__construct($person);

        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * @param \DateTime $startDate
     *
     * @return \CalendarBundle\Entity\Nodes\Event
     */
    public function setStartDate($startDate)
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
     *
     * @return \CalendarBundle\Entity\Nodes\Event
     */
    public function setEndDate($endDate)
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
     * @param \CommonBundle\Entity\General\Language $language
     *
     * @return \CalendarBundle\Entity\Nodes\Translation
     */
    public function getTranslation(Language $language)
    {
        foreach($this->translations as $translation) {
            if ($translation->getLanguage() == $language)
                return $translation;
        }
    }

    /**
     * @param \CommonBundle\Entity\General\Language $language
     *
     * @return string
     */
    public function getLocation(Language $language)
    {
        $translation = $this->getTranslation($language);
        if (null !== $translation)
            return $translation->getLocation();
    }

    /**
     * @param \CommonBundle\Entity\General\Language $language
     *
     * @return string
     */
    public function getTitle(Language $language)
    {
        $translation = $this->getTranslation($language);
        if (null !== $translation)
            return $translation->getTitle();
    }

    /**
     * @param \CommonBundle\Entity\General\Language $language
     *
     * @return string
     */
    public function getName(Language $language)
    {
        $translation = $this->getTranslation($language);
        if (null !== $translation)
            return $translation->getName();
    }

    /**
     * @param \CommonBundle\Entity\General\Language $language
     *
     * @return string
     */
    public function getContent(Language $language)
    {
        $translation = $this->getTranslation($language);
        if (null !== $translation)
            return $translation->getContent();
    }
}

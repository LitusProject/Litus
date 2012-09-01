<?php

namespace CalendarBundle\Entity\Nodes;

use CommonBundle\Entity\General\Language,
    CommonBundle\Entity\Users\Person,
    DateTime,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="CalendarBundle\Repository\Nodes\Event")
 * @ORM\Table(name="nodes.events")
 */
class Event extends \CommonBundle\Entity\Nodes\Node
{
    /**
     * @var array The translations of this event
     *
     * @ORM\OneToMany(targetEntity="CalendarBundle\Entity\Nodes\Translation", mappedBy="event", cascade={"remove"})
     */
    private $translations;

    /**
     * @var \DateTime The start date of this event
     *
     * @ORM\Column(name="start_date", type="datetime")
     */
    private $startDate;

    /**
     * @var \DateTime The end date of this event
     *
     * @ORM\Column(name="end_date", type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * @var string The poster of this event
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $poster;

    /**
     * @var string The name of this page
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @param \CommonBundle\Entity\Users\Person $person
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     */
    public function __construct(Person $person, DateTime $startDate, DateTime $endDate = null)
    {
        parent::__construct($person);

        $this->name = $startDate->format('d_m_Y_H_i');
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
     * @return string
     */
    public function getPoster()
    {
        return $this->poster;
    }

    /**
     * @param string $poster
     *
     * @return \CalendarBundle\Entity\Nodes\Event
     */
    public function setPoster($poster)
    {
        $this->poster = trim($poster, '/');
        return $this;
    }

    /**
     * @param \CommonBundle\Entity\General\Language $language
     * @param boolean $allowFallback
     *
     * @return \CalendarBundle\Entity\Nodes\Translation
     */
    public function getTranslation(Language $language = null, $allowFallback = true)
    {
        foreach($this->translations as $translation) {
            if (null !== $language && $translation->getLanguage() == $language)
                return $translation;

            if ($translation->getLanguage()->getAbbrev() == \Locale::getDefault())
                $fallbackTranslation = $translation;
        }

        if ($allowFallback)
            return $fallbackTranslation;

        return null;
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
    public function getContent(Language $language)
    {
        $translation = $this->getTranslation($language);
        if (null !== $translation)
            return $translation->getContent();
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
     *
     * @return \NewsBundle\Entity\Nodes\News
     */
    public function updateName()
    {
        $translation = $this->getTranslation();
        $this->name = $this->getStartDate()->format('d_m_Y_H_i_s') . '_' . \CommonBundle\Component\Util\Url::createSlug($translation->getTitle());
        return $this;
    }
}

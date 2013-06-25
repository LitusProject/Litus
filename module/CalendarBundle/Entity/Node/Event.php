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

namespace CalendarBundle\Entity\Node;

use CommonBundle\Entity\General\Language,
    CommonBundle\Entity\User\Person,
    DateTime,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="CalendarBundle\Repository\Node\Event")
 * @ORM\Table(name="nodes.events")
 */
class Event extends \CommonBundle\Entity\Node
{
    /**
     * @var array The translations of this event
     *
     * @ORM\OneToMany(targetEntity="CalendarBundle\Entity\Node\Translation", mappedBy="event", cascade={"persist", "remove"})
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
     * @param \CommonBundle\Entity\User\Person $person
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     */
    public function __construct(Person $person, DateTime $startDate, DateTime $endDate = null)
    {
        parent::__construct($person);

        $this->name = $startDate->format('d_m_Y_H_i');
        $this->startDate = $startDate;
        $this->endDate = $endDate;

        $this->translations = new ArrayCollection();
    }

    /**
     * @param \DateTime $startDate
     *
     * @return \CalendarBundle\Entity\Node\Event
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
     * @return \CalendarBundle\Entity\Node\Event
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
     * @return \CalendarBundle\Entity\Node\Event
     */
    public function setPoster($poster)
    {
        $this->poster = trim($poster, '/');
        return $this;
    }

    /**
     * @param \CommonBundle\Entity\General\Language $language
     * @param boolean $allowFallback
     * @return \CalendarBundle\Entity\Node\Translation
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
     * @param boolean $allowFallback
     * @return string
     */
    public function getLocation(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getTranslation($language, $allowFallback);

        if (null !== $translation)
            return $translation->getLocation();

        return '';
    }

    /**
     * @param \CommonBundle\Entity\General\Language $language
     * @param boolean $allowFallback
     * @return string
     */
    public function getTitle(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getTranslation($language, $allowFallback);

        if (null !== $translation)
            return $translation->getTitle();

        return '';
    }

    /**
     * @param \CommonBundle\Entity\General\Language $language
     * @param boolean $allowFallback
     * @return string
     */
    public function getContent(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getTranslation($language, $allowFallback);

        if (null !== $translation)
            return $translation->getContent();

        return '';
    }

    /**
     * @param integer $length
     * @param \CommonBundle\Entity\General\Language $language
     * @param boolean $allowFallback
     * @return string
     */
    public function getSummary($length = 100, Language $language = null, $allowFallback = true)
    {
        $translation = $this->getTranslation($language, $allowFallback);

        if (null !== $translation)
            return $translation->getSummary($length);

        return '';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param \CalendarBundle\Entity\Node\Translation $translation
     *
     * @return \CalendarBundle\Entity\Node\Event
     */
    public function addTranslation(Translation $translation)
    {
        $this->translations->add($translation);
        return $this;
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

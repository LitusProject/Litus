<?php

namespace CalendarBundle\Entity\Node;

use CalendarBundle\Entity\Node\Event\Translation;
use CommonBundle\Component\Util\Url as UrlUtil;
use CommonBundle\Entity\General\Language;
use CommonBundle\Entity\User\Person;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Locale;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="CalendarBundle\Repository\Node\Event")
 * @ORM\Table(name="nodes_events")
 */
class Event extends \CommonBundle\Entity\Node
{
    /**
     * @var ArrayCollection The translations of this event
     *
     * @ORM\OneToMany(targetEntity="CalendarBundle\Entity\Node\Event\Translation", mappedBy="event", cascade={"persist", "remove"})
     */
    private $translations;

    /**
     * @var DateTime The start date of this event
     *
     * @ORM\Column(name="start_date", type="datetime")
     */
    private $startDate;

    /**
     * @var DateTime|null The end date of this event
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
     * @var boolean The flag whether the article is old or not
     *
     * @ORM\Column(name="is_history", type="boolean")
     */
    private $isHistory;

    /**
     * @var boolean The flag whether the article is displayed on the page
     *
     * @ORM\Column(name="is_hidden", type="boolean", nullable=true, options={"default" = false})
     */
    private $isHidden;

    /**
     * @var boolean The flag whether the article is a career event
     *
     * @ORM\Column(name="is_career", type="boolean", nullable=true, options={"default" = false})
     */
    private $isCareer;

    /**
     * @var boolean The flag whether the article is a career event
     *
     * @ORM\Column(name="is_eerstejaars", type="boolean", nullable=true, options={"default" = false})
     */
    private $isEerstejaars;

    /**
     * @var boolean The flag whether the article is a career event
     *
     * @ORM\Column(name="is_international", type="boolean", nullable=true, options={"default" = false})
     */
    private $isInternational;

    /**
     * @param Person $person
     */
    public function __construct(Person $person)
    {
        parent::__construct($person);

        $this->translations = new ArrayCollection();
        $this->isHistory = false;
    }

    /**
     * @param DateTime $startDate
     *
     * @return self
     */
    public function setStartDate($startDate)
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
     * @param DateTime|null $endDate
     *
     * @return self
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return DateTime|null
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
     * @return self
     */
    public function setPoster($poster)
    {
        $this->poster = trim($poster, '/');

        return $this;
    }

    /**
     * @param  Language|null $language
     * @param  boolean       $allowFallback
     * @return Translation|null
     */
    public function getTranslation(Language $language = null, $allowFallback = true)
    {
        foreach ($this->translations as $translation) {
            if ($language !== null && $translation->getLanguage() == $language) {
                return $translation;
            }

            if ($translation->getLanguage()->getAbbrev() == Locale::getDefault()) {
                $fallbackTranslation = $translation;
            }
        }

        if ($allowFallback && isset($fallbackTranslation)) {
            return $fallbackTranslation;
        }

        return null;
    }

    /**
     * @param  Language|null $language
     * @param  boolean       $allowFallback
     * @return string
     */
    public function getLocation(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getTranslation($language, $allowFallback);

        if ($translation !== null) {
            return $translation->getLocation();
        }

        return '';
    }

    /**
     * @param  Language|null $language
     * @param  boolean       $allowFallback
     * @return string
     */
    public function getTitle(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getTranslation($language, $allowFallback);

        if ($translation !== null) {
            return $translation->getTitle();
        }

        return '';
    }

    /**
     * @param  Language|null $language
     * @param  boolean       $allowFallback
     * @return string
     */
    public function getContent(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getTranslation($language, $allowFallback);

        if ($translation !== null) {
            return $translation->getContent();
        }

        return '';
    }

    /**
     * @param  integer       $length
     * @param  Language|null $language
     * @param  boolean       $allowFallback
     * @return string
     */
    public function getSummary($length = 100, Language $language = null, $allowFallback = true)
    {
        $translation = $this->getTranslation($language, $allowFallback);

        if ($translation !== null) {
            return $translation->getSummary($length);
        }

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
     * @param Translation $translation
     *
     * @return self
     */
    public function addTranslation(Translation $translation)
    {
        $this->translations->add($translation);
        $this->updateName();

        return $this;
    }

    /**
     *
     * @return self
     */
    public function updateName()
    {
        $translation = $this->getTranslation();
        $this->name = $this->getStartDate()->format('d_m_Y_H_i_s') . '_' . UrlUtil::createSlug($translation->getTitle());

        return $this;
    }

    /**
     * @return boolean
     */
    public function isHistory()
    {
        return $this->isHistory;
    }

    /**
     * @param boolean $isHistory
     *
     * @return self
     */
    public function setIsHistory($isHistory)
    {
        $this->isHistory = $isHistory;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isHidden()
    {
        return $this->isHidden;
    }

    /**
     * @param boolean $isHidden
     *
     * @return self
     */
    public function setIsHidden($isHidden)
    {
        $this->isHidden = $isHidden;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isCareer()
    {
        return $this->isCareer;
    }

    /**
     * @param boolean $isCareer
     *
     * @return self
     */
    public function setIsCareer($isCareer)
    {
        $this->isCareer = $isCareer;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isEerstejaars()
    {
        return $this->isEerstejaars;
    }

    /**
     * @param boolean $isEerstejaars
     *
     * @return self
     */
    public function setIsEerstejaars($isEerstejaars)
    {
        $this->isEerstejaars = $isEerstejaars;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isInternational()
    {
        return $this->isInternational;
    }

    /**
     * @param boolean $isInternational
     *
     * @return self
     */
    public function setIsInternational($isInternational)
    {
        $this->isInternational = $isInternational;

        return $this;
    }

    /**
     * @param EntityManager $em
     * @return \TicketBundle\Entity\Event
     */
    public function getTicket(EntityManager $em)
    {
        return $em->getRepository('TicketBundle\Entity\Event')
            ->findOneByEvent($this);
    }

    /**
     * @param EntityManager $em
     * @return boolean
     */
    public function hasTicket(EntityManager $em)
    {
        $tickets = $em->getRepository('TicketBundle\Entity\Event')
            ->findOneByEvent($this);
        if (is_null($tickets)) {
            return false;
        }
//        error_log(json_encode($tickets));
        return (count($tickets) > 0) && $tickets->isStillBookable();
    }

    /**
     * @param EntityManager $em
     * @return boolean
     */
    public function hasActiveShifts(EntityManager $em)
    {
        $shifts = $em->getRepository('ShiftBundle\Entity\Shift')
            ->findByEvent($this);
        if (is_null($shifts)) {
            return true;
        }
        return count($shifts) > 0;
    }
}

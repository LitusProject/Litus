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

namespace CudiBundle\Entity\Sales\Session\OpeningHours;

use CommonBundle\Entity\General\Language,
    CommonBundle\Entity\Users\Person,
    DateTime,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sales\Session\OpeningHours\OpeningHour")
 * @ORM\Table(name="cudi.sales_session_openinghours")
 */
class OpeningHour
{
    /**
     * @var integer The ID of the openinghour
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \DateTime The start time of this period
     *
     * @ORM\Column(name="start_date", type="datetime")
     */
    private $startDate;

    /**
     * @var \DateTime The end time of this period
     *
     * @ORM\Column(name="end_date", type="datetime")
     */
    private $endDate;

    /**
     * @var \CommonBundle\Entity\Users\Person The person who created this entity
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\Users\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var \DateTime The time this entity was created
     *
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection The translations of this opening hour
     *
     * @ORM\OneToMany(targetEntity="CudiBundle\Entity\Sales\Session\OpeningHours\Translation", mappedBy="openingHour", cascade={"remove"})
     */
    private $translations;

    /**
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param \CommonBundle\Entity\Users\Person $person
     */
    public function __construct(DateTime $startDate, DateTime $endDate, Person $person)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->person = $person;
        $this->timestamp = new DateTime();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $startDate
     * @return \CudiBundle\Entity\Sales\Session\OpeningHours\OpeningHours
     */
    public function setStart(DateTime $startDate)
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->endDate;
    }

    /**
     * @param \DateTime $endDate
     * @return \CudiBundle\Entity\Sales\Session\OpeningHours\OpeningHour
     */
    public function setEnd(DateTime $endDate)
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @return \CommonBundle\Entity\Users\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @param \CommonBundle\Entity\General\Language $language
     * @param boolean $allowFallback
     * @return \CudiBundle\Entity\Sales\Session\OpeningHours\Translation
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
     * @return string
     */
    public function getComment(Language $language, $allowFallback = true)
    {
        $translation = $this->getTranslation($language, $allowFallback);

        if (null !== $translation)
            return $translation->getComment();

        return '';
    }
}
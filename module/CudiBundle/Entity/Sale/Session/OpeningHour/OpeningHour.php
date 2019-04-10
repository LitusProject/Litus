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

namespace CudiBundle\Entity\Sale\Session\OpeningHour;

use CommonBundle\Entity\General\Language;
use CommonBundle\Entity\User\Person;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Locale;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sale\Session\OpeningHour\OpeningHour")
 * @ORM\Table(name="cudi_sales_session_openinghours")
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
     * @var DateTime The start time of this period
     *
     * @ORM\Column(name="start_date", type="datetime")
     */
    private $startDate;

    /**
     * @var DateTime The end time of this period
     *
     * @ORM\Column(name="end_date", type="datetime")
     */
    private $endDate;

    /**
     * @var Person The person who created this entity
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var DateTime The time this entity was created
     *
     * @ORM\Column(type="datetime")
     */
    // TODO: Rename to creationTime
    private $timestamp;

    /**
     * @var ArrayCollection The translations of this opening hour
     *
     * @ORM\OneToMany(targetEntity="CudiBundle\Entity\Sale\Session\OpeningHour\Translation", mappedBy="openingHour", cascade={"persist", "remove"})
     */
    private $translations;

    /**
     * @param Person $person
     */
    public function __construct(Person $person)
    {
        $this->person = $person;
        $this->timestamp = new DateTime();

        $this->translations = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return DateTime
     */
    // TODO: Rename to getStartDate()
    public function getStart()
    {
        return $this->startDate;
    }

    /**
     * @param  DateTime $startDate
     * @return self
     */
    // TODO: Rename to setStartDate()
    public function setStart(DateTime $startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @return DateTime
     */
    // TODO: Rename to getEndDate()
    public function getEnd()
    {
        return $this->endDate;
    }

    /**
     * @param  DateTime $endDate
     * @return self
     */
    // TODO: Rename to setEndDate()
    public function setEnd(DateTime $endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @return DateTime
     */
    // TODO: Rename to getCreationTime()
    public function getTimestamp()
    {
        return $this->timestamp;
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
     * @param  Language $language
     * @return string
     */
    public function getComment(Language $language, $allowFallback = true)
    {
        $translation = $this->getTranslation($language, $allowFallback);

        if ($translation !== null) {
            return $translation->getComment();
        }

        return '';
    }

    /**
     * @param  Language    $language
     * @param  string|null $comment
     * @return self
     */
    public function setComment(Language $language, $comment = null)
    {
        $translation = $this->getTranslation($language, false);

        if ($comment === null) {
            if ($translation !== null) {
                $this->translations->removeElement($translation);
            }
        } else {
            if ($translation === null) {
                $this->translations->add(new Translation($this, $language, $comment));
            } else {
                $translation->setComment($comment);
            }
        }

        return $this;
    }
}

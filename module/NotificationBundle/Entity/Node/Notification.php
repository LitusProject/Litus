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

namespace NotificationBundle\Entity\Node;

use CommonBundle\Entity\General\Language,
    CommonBundle\Entity\User\Person,
    CommonBundle\Component\Util\Url,
    DateTime,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="NotificationBundle\Repository\Node\Notification")
 * @ORM\Table(name="nodes.notifications")
 */
class Notification extends \CommonBundle\Entity\Node
{
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection The translations of this notification item
     *
     * @ORM\OneToMany(targetEntity="NotificationBundle\Entity\Node\Translation", mappedBy="notification", cascade={"persist", "remove"})
     */
    private $translations;

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
     * @var boolean The flag whether the notification is active or not.
     *
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @param \CommonBundle\Entity\User\Person $person
     * @param string $category
     */
    public function __construct(Person $person, DateTime $startDate, DateTime $endDate, $active )
    {
        parent::__construct($person);
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->active = $active;
        $this->translations = new ArrayCollection();
    }

    /**
     * @param DateTime $startDate
     *
     * @return \NotificationBundle\Entity\Node\Notification
     */
    public function setStartDate($startDate) {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStartDate() {
        return $this->startDate;
    }

    /**
     * @param DateTime $endDate
     *
     * @return \NotificationBundle\Entity\Node\Notification
     */
    public function setEndDate($endDate) {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEndDate() {
        return $this->endDate;
    }

    /**
     * @param boolean $active
     *
     * @return \NotificationBundle\Entity\Node\Notification
     */
    public function setActive($active) {
        $this->active = $active;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isActive() {
        return $this->active;
    }

    /**
     * @param \NotificationBundle\Entity\Node\Translation $translation
     * @return \NotificationBundle\Entity\Node\Notification
     */
    public function addTranslation(Translation $translation)
    {
        $this->translations->add($translation);
        return $this;
    }

    /**
     * @param \CommonBundle\Entity\General\Language $language
     * @param boolean $allowFallback
     * @return \NotificationBundle\Entity\Node\Translation
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
    public function getContent(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getTranslation($language, $allowFallback);

        if (null !== $translation)
            return $translation->getContent();

        return '';
    }

    /**
     * @param \CommonBundle\Entity\General\Language $language
     * @param boolean $allowFallback
     * @return string
     */
    public function getSummary($length = 200, Language $language = null, $allowFallback = true)
    {
        $translation = $this->getTranslation($language, $allowFallback);

        if (null !== $translation)
            return $translation->getSummary($length);

        return '';
    }

}

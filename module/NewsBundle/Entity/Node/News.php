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
 *
 * @license http://litus.cc/LICENSE
 */

namespace NewsBundle\Entity\Node;

use CommonBundle\Entity\General\Language,
    CommonBundle\Entity\User\Person,
    CommonBundle\Component\Util\Url,
    DateTime,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="NewsBundle\Repository\Node\News")
 * @ORM\Table(name="nodes.news")
 */
class News extends \CommonBundle\Entity\Node
{
    /**
     * @var ArrayCollection The translations of this news item
     *
     * @ORM\OneToMany(targetEntity="NewsBundle\Entity\Node\Translation", mappedBy="news", cascade={"persist", "remove"})
     */
    private $translations;

    /**
     * @var string The name of this news item
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var DateTime The date this newsitem will disappear
     *
     * @ORM\Column(name="end_date", type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * @param Person $person
     */
    public function __construct(Person $person)
    {
        parent::__construct($person);

        $this->translations = new ArrayCollection();
    }

    /**
     * @return DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param  DateTime $endDate
     * @return self
     */
    public function setEndDate(DateTime $endDate = null)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @param  Translation $translation
     * @return self
     */
    public function addTranslation(Translation $translation)
    {
        $this->translations->add($translation);

        return $this;
    }

    /**
     * @param  Language|null    $language
     * @param  boolean          $allowFallback
     * @return Translation|null
     */
    public function getTranslation(Language $language = null, $allowFallback = true)
    {
        foreach ($this->translations as $translation) {
            if (null !== $language && $translation->getLanguage() == $language)
                return $translation;

            if ($translation->getLanguage()->getAbbrev() == \Locale::getDefault())
                $fallbackTranslation = $translation;
        }

        if ($allowFallback && isset($fallbackTranslation))
            return $fallbackTranslation;

        return null;
    }

    /**
     * @param  Language|null $language
     * @param  boolean       $allowFallback
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
     * @param  Language|null $language
     * @param  boolean       $allowFallback
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
     * @param  Language|null $language
     * @param  boolean       $allowFallback
     * @return string
     */
    public function getSummary($length = 200, Language $language = null, $allowFallback = true)
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
     * @return self
     */
    public function updateName()
    {
        $this->name = $this->getCreationTime()->format('d_m_Y_H_i_s') . '_' . Url::createSlug($this->getTranslation()->getTitle());

        return $this;
    }
}

<?php

namespace NewsBundle\Entity\Node;

use CommonBundle\Component\Util\Url;
use CommonBundle\Entity\General\Language;
use CommonBundle\Entity\User\Person;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Locale;
use NewsBundle\Entity\Node\News\Translation;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="NewsBundle\Repository\Node\News")
 * @ORM\Table(name="nodes_news")
 */
class News extends \CommonBundle\Entity\Node
{
    /**
     * @var ArrayCollection The translations of this news item
     *
     * @ORM\OneToMany(targetEntity="NewsBundle\Entity\Node\News\Translation", mappedBy="news", cascade={"persist", "remove"})
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
     * @param  DateTime|null $endDate
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
     * @param  Language|null $language
     * @param  boolean       $allowFallback
     * @return string
     */
    public function getSummary($length = 200, Language $language = null, $allowFallback = true)
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
     * @return self
     */
    public function updateName()
    {
        $this->name = $this->getCreationTime()->format('d_m_Y_H_i_s') . '_' . Url::createSlug($this->getTranslation()->getTitle());

        return $this;
    }
}

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

namespace NewsBundle\Entity\Nodes;

use CommonBundle\Entity\General\Language,
    CommonBundle\Entity\User\Person,
    CommonBundle\Component\Util\Url,
    DateTime,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="NewsBundle\Repository\Nodes\News")
 * @ORM\Table(name="nodes.news")
 */
class News extends \CommonBundle\Entity\Node
{
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection The translations of this news item
     *
     * @ORM\OneToMany(targetEntity="NewsBundle\Entity\Nodes\Translation", mappedBy="news", cascade={"persist", "remove"})
     */
    private $translations;

    /**
     * @var string The name of this news item
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var \DateTime The date this newsitem will disappear
     *
     * @ORM\Column(name="end_date", type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * @param \CommonBundle\Entity\User\Person $person
     * @param \DateTime $endDate
     */
    public function __construct(Person $person, DateTime $endDate = null)
    {
        parent::__construct($person);

        $this->name = $this->getCreationTime()->format('d_m_Y_H_i_s');
        $this->translations = new ArrayCollection();
        $this->endDate = $endDate;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param \DateTime $endDate
     * @return \NewsBundle\Entity\Nodes\News
     */
    public function setEndDate(DateTime $endDate = null)
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @param \NewsBundle\Entity\Nodes\Translation $translation
     * @return \NewsBundle\Entity\Nodes\News
     */
    public function addTranslation(Translation $translation)
    {
        $this->translations->add($translation);
        return $this;
    }

    /**
     * @param \CommonBundle\Entity\General\Language $language
     * @param boolean $allowFallback
     * @return \NewsBundle\Entity\Nodes\Translation
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

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return \NewsBundle\Entity\Nodes\News
     */
    public function updateName()
    {
        $this->name = $this->getCreationTime()->format('d_m_Y_H_i_s') . '_' . Url::createSlug($this->getTranslation()->getTitle());
        return $this;
    }
}

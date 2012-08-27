<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace NewsBundle\Entity\Nodes;

use CommonBundle\Entity\General\Language,
    CommonBundle\Entity\Users\Person,
    Doctrine\Common\Collections\ArrayCollection;

/**
 * This entity stores the node item.
 *
 * @Entity(repositoryClass="NewsBundle\Repository\Nodes\News")
 * @Table(name="nodes.news")
 */
class News extends \CommonBundle\Entity\Nodes\Node
{
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection The translations of this news item
     *
     * @OneToMany(targetEntity="NewsBundle\Entity\Nodes\Translation", mappedBy="news", cascade={"persist", "remove"})
     */
    private $translations;

    /**
     * @var string The name of this news item
     *
     * @Column(type="string")
     */
    private $name;

    /**
     * @param \CommonBundle\Entity\Users\Person $person
     * @param string $category
     */
    public function __construct(Person $person)
    {
        parent::__construct($person);

        $this->name = $this->getCreationTime()->format('d_m_Y_H_i_s');
        $this->translations = new ArrayCollection();
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

            if ($translation->getLanguage() == \Zend\Registry::get('Litus_Localization_FallbackLanguage'))
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
     * @return \NewsBundle\Entity\Nodes\News
     */
    public function updateName()
    {
        $translation = $this->getTranslation();
        $this->name = $this->getCreationTime()->format('d_m_Y_H_i_s') . '_' . \CommonBundle\Component\Util\Url::createSlug($translation->getTitle());
        return $this;
    }
}

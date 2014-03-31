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

namespace PageBundle\Entity;

use CommonBundle\Entity\General\Language,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM,
    PageBundle\Entity\Node\Page,
    Locale;

/**
 * This entity stores a category.
 *
 * @ORM\Entity(repositoryClass="PageBundle\Repository\Category")
 * @ORM\Table(name="nodes.pages_categories")
 */
class Category
{
    /**
     * @var int The ID of this category
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \PageBundle\Entity\Node\Page The category's parent
     *
     * @ORM\ManyToOne(targetEntity="PageBundle\Entity\Node\Page")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id")
     */
    private $parent;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection The translations of this category
     *
     * @ORM\OneToMany(targetEntity="PageBundle\Entity\Category\Translation", mappedBy="category", cascade={"remove"})
     */
    private $translations;

    /**
     * @var bool Whether or not the category is active
     *
     * @ORM\Column(type="boolean")
     */
    private $active;

    public function __construct()
    {
        $this->active = true;

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
     * @return \PageBundle\Entity\Node\Page
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return \PageBundle\Entity\Category
     */
    public function setParent(Page $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @param  \CommonBundle\Entity\General\Language   $language
     * @param  boolean                                 $allowFallback
     * @return \PageBundle\Entity\Category\Translation
     */
    public function getTranslation(Language $language = null, $allowFallback = true)
    {
        $fallbackTranslation = null;

        foreach ($this->translations as $translation) {
            if (null !== $language && $translation->getLanguage() == $language)
                return $translation;

            if ($translation->getLanguage()->getAbbrev() == Locale::getDefault())
                $fallbackTranslation = $translation;
        }

        if ($allowFallback)
            return $fallbackTranslation;

        return null;
    }

    /**
     * @param  \CommonBundle\Entity\General\Language $language
     * @param  boolean                               $allowFallback
     * @return string
     */
    public function getName(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getTranslation($language, $allowFallback);

        if (null !== $translation)
            return $translation->getName();

        return '';
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @return void
     */
    public function deactivate()
    {
        $this->active = false;
    }
}

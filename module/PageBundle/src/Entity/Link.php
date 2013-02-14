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

namespace PageBundle\Entity;

use CommonBundle\Entity\General\Language,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM,
    PageBundle\Entity\Category,
    PageBundle\Entity\Nodes\Page;

/**
 * This entity represents a link in the menu structure.
 *
 * @ORM\Entity(repositoryClass="PageBundle\Repository\Link")
 * @ORM\Table(name="nodes.pages_links")
 */
class Link
{
    /**
     * @var int The ID of this link
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \PageBundle\Entity\Nodes\Page The link's parent
     *
     * @ORM\ManyToOne(targetEntity="PageBundle\Entity\Nodes\Page")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id")
     */
    private $parent;

    /**
     * @var \PageBundle\Entity\Nodes\Page The link's category
     *
     * @ORM\ManyToOne(targetEntity="PageBundle\Entity\Category")
     * @ORM\JoinColumn(name="category", referencedColumnName="id")
     */
    private $category;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection The translations of this link
     *
     * @ORM\OneToMany(targetEntity="PageBundle\Entity\Links\Translation", mappedBy="link", cascade={"remove"})
     */
    private $translations;

    /**
     * @param \PageBundle\Entity\Category $category
     */
    public function __construct(Category $category)
    {
        $this->category = $category;

        $this->translations = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \PageBundle\Entity\Nodes\Page
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param \PageBundle\Entity\Nodes\Page $category The page's category
     * @return \PageBundle\Entity\Category
     */
    public function setParent(Page $parent)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return \PageBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param \PageBundle\Entity\Category $category
     * @return \PageBundle\Entity\Link
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @param \CommonBundle\Entity\General\Language $language
     * @param boolean $allowFallback
     * @return \PageBundle\Entity\Links\Translation
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
    public function getName(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getTranslation($language, $allowFallback);

        if (null !== $translation)
            return $translation->getName();

        return '';
    }

    /**
     * @param \CommonBundle\Entity\General\Language $language
     * @param boolean $allowFallback
     * @return string
     */
    public function getUrl(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getTranslation($language, $allowFallback);

        if (null !== $translation)
            return $translation->getUrl();

        return '';
    }
}

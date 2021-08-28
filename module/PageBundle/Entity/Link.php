<?php

namespace PageBundle\Entity;

use CommonBundle\Entity\General\Language;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Locale;
use PageBundle\Entity\Category;
use PageBundle\Entity\Node\Page;

/**
 * This entity represents a link in the menu structure.
 *
 * @ORM\Entity(repositoryClass="PageBundle\Repository\Link")
 * @ORM\Table(name="nodes_pages_links")
 */
class Link
{
    /**
     * @var integer The ID of this link
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Page The link's parent
     *
     * @ORM\ManyToOne(targetEntity="PageBundle\Entity\Node\Page")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id")
     */
    private $parent;

    /**
     * @var Category The link's category
     *
     * @ORM\ManyToOne(targetEntity="PageBundle\Entity\Category")
     * @ORM\JoinColumn(name="category", referencedColumnName="id")
     */
    private $category;

    /**
     * @var ArrayCollection The translations of this link
     *
     * @ORM\OneToMany(targetEntity="PageBundle\Entity\Link\Translation", mappedBy="link", cascade={"remove"})
     */
    private $translations;

    /**
     * @var integer|null The ordering number for the page in the category
     *
     * @ORM\Column(name="order_number", type="integer", nullable=true)
     */
    private $orderNumber;

    /**
     * @var Language|null The Language of the forced language (null if it's a normal page)
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Language")
     * @ORM\JoinColumn(name="forced_language", referencedColumnName="id", nullable=true)
     */
    private $forcedLanguage;

    /**
     * @var boolean reflects if the page is active.
     *
     * @ORM\Column(name="active", type="boolean", options={"default" = true})
     */
    private $active;

    public function __construct()
    {
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
     * @return Page
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param  Page|null $parent
     * @return self
     */
    public function setParent(Page $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param  Category $category
     * @return self
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @param  Language|null $language
     * @param  boolean       $allowFallback
     * @return \PageBundle\Entity\Link\Translation|null
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
    public function getName(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getTranslation($language, $allowFallback);

        if ($translation !== null) {
            return $translation->getName();
        }

        return '';
    }

    /**
     * @param  Language|null $language
     * @param  boolean       $allowFallback
     * @return string
     */
    public function getUrl(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getTranslation($language, $allowFallback);

        if ($translation !== null) {
            return $translation->getUrl();
        }

        return '';
    }

    /**
     * @return integer
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    /**
     * @param $orderNumber
     * @return self
     */
    public function setOrderNumber($orderNumber)
    {
        // phpcs:disable SlevomatCodingStandard.Classes.ModernClassNameReference.ClassNameReferencedViaFunctionCall
        if (get_class($orderNumber) !== 'int') {
        // phpcs:enable
            $this->orderNumber = null;
        } else {
            $this->orderNumber = $orderNumber;
        }
        return $this;
    }

    /**
     * @return Language|null
     */
    public function getForcedLanguage()
    {
        return $this->forcedLanguage;
    }

    /**
     * @param $forcedLanguage
     * @return self
     */
    public function setForcedLanguage($forcedLanguage)
    {
        // phpcs:disable SlevomatCodingStandard.Classes.ModernClassNameReference.ClassNameReferencedViaFunctionCall
        if (get_class($forcedLanguage) !== Language::class) {
        // phpcs:enable
            $this->forcedLanguage = null;
        } else {
            $this->forcedLanguage = $forcedLanguage;
        }
        return $this;
    }

    /**
     * @param Language $lang
     * @return boolean
     */
    public function isLanguageAvailable(Language $lang)
    {
        return $this->getForcedLanguage() == null || $this->getForcedLanguage() === $lang;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     * @return self
     */
    public function setActive(bool $active)
    {
        $this->active = $active;
        return $this;
    }
}

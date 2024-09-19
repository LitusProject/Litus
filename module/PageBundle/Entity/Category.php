<?php

namespace PageBundle\Entity;

use CommonBundle\Entity\General\Language;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Locale;
use PageBundle\Entity\Node\Page;

/**
 * This entity stores a category.
 *
 * @ORM\Entity(repositoryClass="PageBundle\Repository\Category")
 * @ORM\Table(name="nodes_pages_categories")
 */
class Category
{
    /**
     * @var integer The ID of this category
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Page The category's parent
     *
     * @ORM\ManyToOne(targetEntity="PageBundle\Entity\Node\Page")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id")
     */
    private $parent;

    /**
     * @var ArrayCollection The translations of this category
     *
     * @ORM\OneToMany(targetEntity="PageBundle\Entity\Category\Translation", mappedBy="category", cascade={"remove"})
     */
    private $translations;

    /**
     * @var boolean Whether or not the category is active
     *
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @var integer|null The ordering number for the category
     *
     * @ORM\Column(name="order_number", type="integer", nullable=true)
     */
    private $orderNumber;

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
     * @param  Language|null $language
     * @param  boolean       $allowFallback
     * @return \PageBundle\Entity\Category\Translation|null
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
        $orderNumber = intval($orderNumber);
        if ($orderNumber === null || $orderNumber === 0) {
            $this->orderNumber = null;
        } else {
            $this->orderNumber = $orderNumber;
        }
        return $this;
    }
}

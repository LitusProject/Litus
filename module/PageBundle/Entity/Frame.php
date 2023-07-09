<?php

namespace PageBundle\Entity;


use CommonBundle\Entity\General\Language;
use Doctrine\Common\Collections\ArrayCollection;
use Locale;
use PageBundle\Entity\Frame\Translation;
use PageBundle\Entity\Node\CategoryPage;
use PageBundle\Entity\Node\Page;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity represents a frame in a CategoryPage.
 *
 * @ORM\Entity(repositoryClass="PageBundle\Repository\Frame")
 * @ORM\Table(name="frame")
 */
class Frame
{
    /**
     * @var integer The ID of this frame
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var CategoryPage The frame's categoryPage
     *
     * @ORM\ManyToOne(targetEntity="PageBundle\Entity\Node\CategoryPage")
     * @ORM\JoinColumn(name="categoryPage", referencedColumnName="id")
     */
    private $categoryPage;

    /**
     * @var Page|Link The frame's page or link to refer to
     *
     * @ORM\JoinColumn(name="link_to", referencedColumnName="id")
     */
    private $linkTo;

    /**
     * @var ArrayCollection The translations of this frame
     *
     * @ORM\OneToMany(targetEntity="PageBundle\Entity\Frame\Translation", mappedBy="frame", cascade={"remove"})
     */
    private $translations;

    /**
     * @var boolean reflects if the frame is active.
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
     * @return CategoryPage
     */
    public function getCategoryPage()
    {
        return $this->categoryPage;
    }

    /**
     * @param CategoryPage $categoryPage
     * @return self
     */
    public function setCategoryPage(CategoryPage $categoryPage)
    {
        $this->categoryPage = $categoryPage;

        return $this;
    }

    /**
     * @return Page|Link
     */
    public function getLinkTo()
    {
        return $this->linkTo;
    }

    /**
     * @param Page|Link $linkTo
     * @return self
     */
    public function setLinkTo($linkTo)
    {
        $this->linkTo = $linkTo;

        return $this;
    }

    /**
     * @param Language|null $language
     * @param boolean $allowFallback
     * @return Translation|null
     */
    public function getTranslation(Language $language = null, $allowFallback = true)
    {
        $fallbackTranslation = null;

        foreach ($this->translations as $translation) {
            if ($language !== null && $translation->getLanguage() == $language) {
                return $translation;
            }

            if ($translation->getLanguage()->getAbbrev() == Locale::getDefault()) {
                $fallbackTranslation = $translation;
            }
        }

        if ($allowFallback) {
            return $fallbackTranslation;
        }

        return null;
    }

    /**
     * @param Language|null $language
     * @param boolean $allowFallback
     * @return string
     */
    public function getDescription(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getTranslation($language, $allowFallback);

        if ($translation !== null) {
            return $translation->getDescription();
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
     * @param boolean $active
     * @return self
     */
    public function setActive(bool $active)
    {
        $this->active = $active;
        return $this;
    }
}

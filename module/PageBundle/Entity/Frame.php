<?php

namespace PageBundle\Entity;


use CommonBundle\Entity\General\Language;
use Doctrine\Common\Collections\ArrayCollection;
use Locale;
use PageBundle\Entity\Frame\Translation;
use PageBundle\Entity\Node\Page;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity represents a frame in a CategoryPage.
 *
 * @ORM\Entity(repositoryClass="PageBundle\Repository\Frame")
 * @ORM\Table(name="frames")
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
     * @ORM\ManyToOne(targetEntity="PageBundle\Entity\CategoryPage", )
     * @ORM\JoinColumn(name="categoryPage", referencedColumnName="id", onDelete="CASCADE")
     */
    private $categoryPage;

    /**
     * @var Page The frame's page to refer to
     *
     * @ORM\ManyToOne(targetEntity="PageBundle\Entity\Node\Page")
     * @ORM\JoinColumn(name="link_to_page", referencedColumnName="id", nullable=true)
     */
    private $linkToPage;

    /**
     * @var Link The frame's link to refer to
     *
     * @ORM\ManyToOne(targetEntity="PageBundle\Entity\Link")
     * @ORM\JoinColumn(name="link_to_link", referencedColumnName="id", nullable=true)
     */
    private $linkToLink;

    /**
     * @var boolean reflects if the frame is active.
     *
     * @ORM\Column(name="active", type="boolean", options={"default" = true})
     */
    private $active;

    /**
     * @var boolean reflects if the frame is big.
     *
     * @ORM\Column(name="big", type="boolean", options={"default" = true})
     */
    private $big;

    /**
     * @var boolean reflects if the frame has a description.
     *
     * @ORM\Column(name="has_description", type="boolean", options={"default" = true})
     */
    private $has_description;

    /**
     * @var boolean reflects if the frame has a poster.
     *
     * @ORM\Column(name="has_poster", type="boolean", options={"default" = true})
     */
    private $has_poster;

    /**
     * @var ArrayCollection The translations of this frame
     *
     * @ORM\OneToMany(targetEntity="PageBundle\Entity\Frame\Translation", mappedBy="frame")
     */
    private $translations;

    /**
     * @var string The poster of this frame
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $poster;

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
        return !is_null($this->linkToPage) ? $this->linkToPage : $this->linkToLink;
    }

    /**
     * @param Page|Link $linkTo
     * @return self
     */
    public function setLinkTo($linkTo)
    {
        if ($linkTo instanceof Page) {
            $this->linkToPage = $linkTo;
            $this->linkToLink = null;
        } else {
            $this->linkToPage = null;
            $this->linkToLink = $linkTo;
        }

        return $this;
    }

    /**
     * @return boolean
     */
    public function doesLinkToPage(){
        return !is_null($this->linkToPage);
    }

    /**
     * @return boolean
     */
    public function doesLinkToLink(){
        return !is_null($this->linkToLink);
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

    /**
     * @return boolean
     */
    public function isBig()
    {
        return $this->big;
    }

    /**
     * @param boolean $big
     * @return self
     */
    public function setBig(bool $big)
    {
        $this->big = $big;
        return $this;
    }

    /**
     * @return boolean
     */
    public function hasDescription()
    {
        return $this->has_description;
    }

    /**
     * @param boolean $has_description
     * @return self
     */
    public function setHasDescription(bool $has_description)
    {
        $this->has_description = $has_description;
        return $this;
    }

    /**
     * @return boolean
     */
    public function hasPoster()
    {
        return $this->has_poster;
    }

    /**
     * @param boolean $has_poster
     * @return self
     */
    public function setHasPoster(bool $has_poster)
    {
        $this->has_poster = $has_poster;
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
    public function getTitle(Language $language = null, $allowFallback = true)
    {
        if(!is_null($this->linkToPage)){
            return $this->linkToPage->getTitle($language, $allowFallback);
        } else if ((!is_null($this->linkToLink))){
            return $this->linkToLink->getName($language, $allowFallback);
        }

        return '';
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
     * @return string
     */
    public function getPoster()
    {
        return $this->poster;
    }

    /**
     * @param string $poster
     *
     * @return self
     */
    public function setPoster($poster)
    {
        $this->poster = trim($poster, '/');

        return $this;
    }
}

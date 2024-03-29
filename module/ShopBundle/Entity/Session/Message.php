<?php

namespace ShopBundle\Entity\Session;

use CommonBundle\Entity\General\Language;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Locale;
use ShopBundle\Entity\Session\Message\Translation;

/**
 * @ORM\Entity(repositoryClass="ShopBundle\Repository\Session\Message")
 * @ORM\Table(name="shop_sessions_messages")
 */
class Message
{
    /**
     * @var integer The ID of the message
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var DateTime The time this entity was created
     *
     * @ORM\Column(name="creation_time", type="datetime")
     */
    private $creationTime;

    /**
     * @var boolean Is active on home page
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @var ArrayCollection The translations of this message
     *
     * @ORM\OneToMany(targetEntity="ShopBundle\Entity\Session\Message\Translation", mappedBy="message", cascade={"persist", "remove"})
     */
    private $translations;

    public function __construct()
    {
        $this->creationTime = new DateTime();

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
     * @return DateTime
     */
    public function getCreationTime()
    {
        return $this->creationTime;
    }

    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param $active
     * @return self
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
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
     * @param  Language $language
     * @return string
     */
    public function getTopContent(Language $language, $allowFallback = true)
    {
        $translation = $this->getTranslation($language, $allowFallback);

        if ($translation !== null) {
            return $translation->getTopContent();
        }

        return '';
    }

    /**
     * @param  Language $language
     * @return string
     */
    public function getBottomContent(Language $language, $allowFallback = true)
    {
        $translation = $this->getTranslation($language, $allowFallback);

        if ($translation !== null) {
            return $translation->getBottomContent();
        }

        return '';
    }

    /**
     * @param  Language    $language
     * @param string|null $topContent
     * @param string|null $bottomContent
     * @return self
     */
    public function setContent(Language $language, string $topContent = null, string $bottomContent = null)
    {
        $translation = $this->getTranslation($language, false);

        if ($topContent === null && $bottomContent === null) {
            if ($translation !== null) {
                $this->translations->removeElement($translation);
            }
        } else {
            if ($translation === null) {
                $this->translations->add(new Translation($this, $language, $topContent, $bottomContent));
            } else {
                $translation->setTopContent($topContent);
                $translation->setBottomContent($bottomContent);
            }
        }

        return $this;
    }
}

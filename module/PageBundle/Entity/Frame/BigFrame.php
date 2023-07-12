<?php

namespace PageBundle\Entity\Frame;

use CommonBundle\Entity\General\Language;
use Doctrine\Common\Collections\ArrayCollection;
use Locale;
use PageBundle\Entity\Frame\BigFrame\Translation;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the big frame item.
 *
 * @ORM\Entity(repositoryClass="PageBundle\Repository\Frame\BigFrame")
 * @ORM\Table(name="frames_big")
 */
class BigFrame extends \PageBundle\Entity\Frame
{
    /**
     * @var ArrayCollection The translations of this frame
     *
     * @ORM\OneToMany(targetEntity="PageBundle\Entity\Frame\BigFrame\Translation", mappedBy="frame", cascade={"remove"})
     */
    private $translations;

    public function __construct()
    {
        parent::__construct();

        $this->translations = new ArrayCollection();
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
}
<?php

namespace FormBundle\Entity\Field;

use CommonBundle\Entity\General\Language;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FormBundle\Entity\Node\Form;
use Locale;

/**
 * A class that stores a number of options.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Field\Dropdown")
 * @ORM\Table(name="form_fields_dropdowns")
 */
class Dropdown extends \FormBundle\Entity\Field
{
    /**
     * @var ArrayCollection The translations of this field
     *
     * @ORM\OneToMany(targetEntity="FormBundle\Entity\Field\Translation\Option", mappedBy="field", cascade={"remove"})
     */
    private $optionTranslations;

    /**
     * @param Form $form
     */
    public function __construct(Form $form)
    {
        parent::__construct($form);

        $this->optionTranslations = new ArrayCollection();
    }

    /**
     * @param  Language|null $language
     * @param  boolean       $allowFallback
     * @return string
     */
    public function getOptions(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getOptionTranslation($language, $allowFallback);

        if ($translation !== null) {
            return $translation->getOptions();
        }

        return '';
    }

    /**
     * @param  Language|null $language
     * @param  boolean       $allowFallback
     * @return array
     */
    public function getOptionsArray(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getOptionTranslation($language, $allowFallback);

        if ($translation !== null) {
            return $translation->getOptionsArray();
        }

        return array();
    }

    /**
     * @param  Language|null $language
     * @param  boolean       $allowFallback
     * @return \FormBundle\Entity\Field\Translation\Option
     */
    public function getOptionTranslation(Language $language = null, $allowFallback = true)
    {
        foreach ($this->optionTranslations as $translation) {
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
     * @param  boolean  $value
     * @return string
     */
    public function getValueString(Language $language, $value)
    {
        if (isset($this->getOptionsArray($language)[$value])) {
            return $this->getOptionsArray($language)[$value];
        }

        return '';
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'dropdown';
    }
}

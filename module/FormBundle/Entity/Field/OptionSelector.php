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

namespace FormBundle\Entity\Field;

use CommonBundle\Entity\General\Language,
    Doctrine\ORM\Mapping as ORM,
    FormBundle\Entity\Field,
    FormBundle\Entity\Node\Form;

/**
 * An abstract class that stores a number of options.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Field\OptionSelector")
 * @ORM\Table(name="forms.fields_options")
 */
abstract class OptionSelector extends Field
{
    /**
     * @var array The translations of this field
     *
     * @ORM\OneToMany(targetEntity="FormBundle\Entity\Field\Translation\Option", mappedBy="field", cascade={"remove"})
     */
    private $optionTranslations;

    /**
     * @param FormBundle\Entity\Node\Form $form
     * @param integer                     $order
     * @param boolean                     $required
     * @param \FormBundle\Entity\Field    $visibityDecisionField
     * @param string                      $visibilityValue
     */
    public function __construct(Form $form, $order, $required, Field $visibityDecisionField = null, $visibilityValue = null)
    {
        parent::__construct($form, $order, $required, $visibityDecisionField, $visibilityValue);
    }

    /**
     * @param  \CommonBundle\Entity\General\Language $language
     * @param  boolean                               $allowFallback
     * @return string
     */
    public function getOptions(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getOptionTranslation($language, $allowFallback);

        if (null !== $translation)
            return $translation->getOptions();

        return '';
    }

    /**
     * @param  \CommonBundle\Entity\General\Language $language
     * @param  boolean                               $allowFallback
     * @return array
     */
    public function getOptionsArray(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getOptionTranslation($language, $allowFallback);

        if (null !== $translation)
            return $translation->getOptionsArray();

        return array();
    }

    /**
     * @param  \CommonBundle\Entity\General\Language       $language
     * @param  boolean                                     $allowFallback
     * @return \FormBundle\Entity\Field\Translation\Option
     */
    public function getOptionTranslation(Language $language = null, $allowFallback = true)
    {

        foreach ($this->optionTranslations as $translation) {
            if (null !== $language && $translation->getLanguage() == $language)
                return $translation;

            if ($translation->getLanguage()->getAbbrev() == \Locale::getDefault())
                $fallbackTranslation = $translation;
        }

        if ($allowFallback && isset($fallbackTranslation))
            return $fallbackTranslation;

        return null;
    }

    /**
     * @param  \CommonBundle\Entity\General\Language $language
     * @param  boolean                               $value
     * @return string
     */
    public function getValueString(Language $language, $value)
    {
        if (isset($this->getOptionsArray($language)[$value]))
            return $this->getOptionsArray($language)[$value];

        return '';
    }
}

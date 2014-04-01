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

namespace FormBundle\Entity\Field\Translation;

use CommonBundle\Entity\General\Language,
    FormBundle\Entity\Field\OptionSelector,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Field\Translation\Option")
 * @ORM\Table(name="forms.fields_options_translations")
 */
class Option
{
    /**
     * @var int The ID of this tanslation
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var OptionSelector The field of this translation
     *
     * @ORM\ManyToOne(targetEntity="FormBundle\Entity\Field\OptionSelector", inversedBy="optionTranslations")
     * @ORM\JoinColumn(name="field", referencedColumnName="id")
     */
    private $field;

    /**
     * @var Language The language of this tanslation
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Language")
     * @ORM\JoinColumn(name="language", referencedColumnName="id")
     */
    private $language;

    /**
     * @var string The options of this tanslation
     *
     * @ORM\Column(type="text")
     */
    private $options;

    /**
     * @param OptionSelector field
     * @param Language $language
     * @param string   $options
     */
    public function __construct(OptionSelector $field, Language $language, $options)
    {
        $this->field = $field;
        $this->language = $language;
        $this->options = $options;
    }

    /**
     * @return OptionSelector
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return Language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $options
     *
     * @return self
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return string The options in a comma-seperated string.
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return array The options in an array.
     */
    public function getOptionsArray()
    {
        return explode(',', $this->getOptions());
    }
}

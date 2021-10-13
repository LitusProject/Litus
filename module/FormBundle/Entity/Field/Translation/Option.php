<?php

namespace FormBundle\Entity\Field\Translation;

use CommonBundle\Entity\General\Language;
use Doctrine\ORM\Mapping as ORM;
use FormBundle\Entity\Field\Dropdown;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Field\Translation\Option")
 * @ORM\Table(name="form_fields_translations_options")
 */
class Option
{
    /**
     * @var integer The ID of this tanslation
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Dropdown The field of this translation
     *
     * @ORM\ManyToOne(targetEntity="FormBundle\Entity\Field\Dropdown", inversedBy="optionTranslations")
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
     * @param Dropdown $field
     * @param Language $language
     * @param string   $options
     */
    public function __construct(Dropdown $field, Language $language, $options)
    {
        $this->field = $field;
        $this->language = $language;
        $this->options = $options;
    }

    /**
     * @var int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Dropdown
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

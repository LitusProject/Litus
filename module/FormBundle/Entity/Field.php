<?php

namespace FormBundle\Entity;

use CommonBundle\Entity\General\Language;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FormBundle\Entity\Field\Translation;
use FormBundle\Entity\Node\Form;
use Locale;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Field")
 * @ORM\Table(name="form_fields")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="inheritance_type", type="string")
 * @ORM\DiscriminatorMap({
 *     "checkbox"="FormBundle\Entity\Field\Checkbox",
 *     "dropdown"="FormBundle\Entity\Field\Dropdown",
 *     "file"="FormBundle\Entity\Field\File",
 *     "text"="FormBundle\Entity\Field\Text",
 *     "timeslot"="FormBundle\Entity\Field\TimeSlot"
 * })
 */
abstract class Field
{
    /**
     * @var integer The reservation's unique identifier
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Form The form this field is part of.
     *
     * @ORM\ManyToOne(targetEntity="FormBundle\Entity\Node\Form", inversedBy="fields")
     * @ORM\JoinColumn(name="form_id", referencedColumnName="id")
     */
    private $form;

    /**
     * @var integer The order of this field.
     *
     * @ORM\Column(name="fieldOrder", type="bigint")
     */
    private $order;

    /**
     * @var boolean Indicates whether this is a required field.
     *
     * @ORM\Column(name="required", type="boolean")
     */
    private $required;

    /**
     * @var Field|null The field responsible for the visibility of this field
     *
     * @ORM\ManyToOne(targetEntity="FormBundle\Entity\Field")
     * @ORM\JoinColumn(name="visibility_decission_field", referencedColumnName="id")
     */
    private $visibityDecisionField;

    /**
     * @var string The required value of the visibityDecisionField;
     *
     * @ORM\Column(name="visibility_value", type="string", nullable=true)
     */
    private $visibilityValue;

    /**
     * @var ArrayCollection The translations of this field
     *
     * @ORM\OneToMany(targetEntity="FormBundle\Entity\Field\Translation", mappedBy="field", cascade={"remove"})
     */
    private $translations;

    /**
     * @var array The possible types of a field
     */
    public static $possibleTypes = array(
        'string'   => 'String',
        'dropdown' => 'Dropdown',
        'checkbox' => 'Checkbox',
        'file'     => 'File',
    );

    /**
     * @param Form $form
     */
    public function __construct(Form $form)
    {
        $this->form = $form;
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
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param integer $order
     *
     * @return self
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return integer
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param boolean $required
     *
     * @return self
     */
    public function setRequired($required)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @param Field|null $visibityDecisionField
     *
     * @return self
     */
    public function setVisibilityDecissionField(Field $visibityDecisionField = null)
    {
        $this->visibityDecisionField = $visibityDecisionField;

        return $this;
    }

    /**
     * @return Field|null
     */
    public function getVisibilityDecissionField()
    {
        return $this->visibityDecisionField;
    }

    /**
     * @param string $visibilityValue
     *
     * @return self
     */
    public function setVisibilityValue($visibilityValue)
    {
        $this->visibilityValue = $visibilityValue;

        return $this;
    }

    /**
     * @return string
     */
    public function getVisibilityValue()
    {
        return $this->visibilityValue;
    }

    /**
     * @param Language|null $language
     * @param boolean       $allowFallback
     *
     * @return string
     */
    public function getLabel(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getTranslation($language, $allowFallback);

        if ($translation !== null) {
            return $translation->getLabel();
        }

        return '';
    }

    /**
     * @param Language|null $language
     * @param boolean       $allowFallback
     *
     * @return Translation
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
     * @param  string $value
     * @return string
     */
    abstract public function getValueString(Language $language, $value);

    /**
     * @return string
     */
    abstract public function getType();
}

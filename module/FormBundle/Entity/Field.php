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

namespace FormBundle\Entity;

use CommonBundle\Entity\General\Language,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM,
    FormBundle\Entity\Node\Form;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Field")
 * @ORM\Table(name="forms.fields")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="inheritance_type", type="string")
 * @ORM\DiscriminatorMap({
 *      "string"="FormBundle\Entity\Field\String",
 *      "options"="FormBundle\Entity\Field\OptionSelector",
 *      "dropdown"="FormBundle\Entity\Field\Dropdown",
 *      "checkbox"="FormBundle\Entity\Field\Checkbox",
 *      "file"="FormBundle\Entity\Field\File",
 *      "timeslot"="FormBundle\Entity\Field\TimeSlot"
 * })
 */
abstract class Field
{
    /**
     * @var The reservation's unique identifier
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
     * @var int The order of this field.
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
     * @ORM\OneToMany(targetEntity="FormBundle\Entity\Translation", mappedBy="field", cascade={"remove"})
     */
    private $translations;

    /**
     * @var array The possible types of a field
     */
    public static $POSSIBLE_TYPES = array(
        'string' => 'String',
        'dropdown' => 'Dropdown',
        'checkbox' => 'Checkbox',
        'file' => 'File',
    );

    /**
     * @param Form        $form
     * @param integer     $order
     * @param boolean     $required
     * @param Field|null  $visibityDecisionField
     * @param string|null $visibilityValue
     */
    public function __construct(Form $form, $order, $required, Field $visibityDecisionField = null, $visibilityValue = null)
    {
        $this->form = $form;
        $this->order = $order;
        $this->required = $required;
        $this->translations = new ArrayCollection();
        $this->visibityDecisionField = $visibityDecisionField;
        $this->visibilityValue = $visibilityValue;
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
     * @param int $order
     *
     * @return self
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return int
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
     * @param Field $visibityDecisionField
     *
     * @return self
     */
    public function setVisibilityDecissionField(Field $visibityDecisionField = null)
    {
        $this->visibityDecisionField = $visibityDecisionField;

        return $this;
    }

    /**
     * @return Field
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

        if (null !== $translation)
            return $translation->getLabel();

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
     * @param string $value
     */
    abstract public function getValueString(Language $language, $value);

    abstract public function getType();
}

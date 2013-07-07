<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace FormBundle\Entity;

use CommonBundle\Entity\General\Language,
    CommonBundle\Entity\User\Person,
    CommonBundle\Component\Util\Url,
    DateTime,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM;

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
 *      "checkbox"="FormBundle\Entity\Field\Checkbox"
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
     * @var FormBundle\Entity\Node\Form The form this field is part of.
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
     * @var array The translations of this field
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
    );

    /**
     * @param string $label
     */
    public function __construct($form, $order, $required)
    {
        $this->form = $form;
        $this->order = $order;
        $this->required = $required;
        $this->translations = new ArrayCollection();
    }

    /**
     * @return The identification number of this form.
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return The form this field belongs to.
     */
    public function getForm() {
        return $this->form;
    }

    /**
     * @param int $order
     *
     * @return \FormBundle\Entity\Field
     */
    public function setOrder($order) {
        $this->order = $order;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrder() {
        return $this->order;
    }

    /**
     * @param boolean $required
     *
     * @return \FormBundle\Entity\Field
     */
    public function setRequired($required) {
        $this->required = $required;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isRequired() {
        return $this->required;
    }

    /**
     * @param \CommonBundle\Entity\General\Language $language
     * @param boolean $allowFallback
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
     * @param \CommonBundle\Entity\General\Language $language
     * @param boolean $allowFallback
     *
     * @return \FormBundle\Entity\Translation
     */
    public function getTranslation(Language $language = null, $allowFallback = true)
    {

        foreach($this->translations as $translation) {
            if (null !== $language && $translation->getLanguage() == $language)
                return $translation;

            if ($translation->getLanguage()->getAbbrev() == \Locale::getDefault())
                $fallbackTranslation = $translation;
        }

        if ($allowFallback && isset($fallbackTranslation))
            return $fallbackTranslation;

        return null;
    }

    abstract public function getValueString(Language $language, $value);

}

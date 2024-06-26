<?php

namespace FormBundle\Entity;

use CommonBundle\Entity\General\Language;
use Doctrine\ORM\Mapping as ORM;
use FormBundle\Entity\Node\Entry as NodeEntry;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Entry")
 * @ORM\Table(name="form_entries")
 */
class Entry
{
    /**
     * @var NodeEntry The form entry's id.
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="FormBundle\Entity\Node\Entry", cascade={"persist"})
     * @ORM\JoinColumn(name="form_entry_id", referencedColumnName="id")
     */
    private $formEntry;

    /**
     * @var Field The field this entry is for.
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="FormBundle\Entity\Field", cascade={"persist"})
     * @ORM\JoinColumn(name="form_field_id", referencedColumnName="id")
     */
    private $field;

    /**
     * @var string The value of this field.
     *
     * @ORM\Column(name="value", type="text", nullable=true)
     */
    private $value;

    /**
     * @var string The readable value of this field.
     *
     * @ORM\Column(name="readable_value", type="text", nullable=true)
     */
    private $readableValue;

    /**
     * @param NodeEntry   $formEntry
     * @param Field       $field
     * @param string      $value
     * @param string|null $readableValue
     */
    public function __construct(NodeEntry $formEntry, Field $field, $value, $readableValue = null)
    {
        $this->formEntry = $formEntry;
        $this->field = $field;
        $this->value = $value;
        $this->readableValue = $readableValue;
    }

    /**
     * @return NodeEntry The form entry this entry belongs to.
     */
    public function getFormEntry()
    {
        return $this->formEntry;
    }

    /**
     * @return Field The field this entry belongs to.
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param  string $value
     * @return self
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getReadableValue()
    {
        return $this->readableValue;
    }

    /**
     * @param  string $readableValue
     * @return self
     */
    public function setReadableValue($readableValue)
    {
        $this->readableValue = $readableValue;

        return $this;
    }

    /**
     * @param  Language $language
     * @return string
     */
    public function getValueString(Language $language)
    {
        return $this->getField()->getValueString($language, $this->getValue());
    }
}

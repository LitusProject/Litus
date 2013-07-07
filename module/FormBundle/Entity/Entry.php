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
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Entry")
 * @ORM\Table(name="forms.fieldentries")
 */
class Entry
{

    /**
     * @var FormBundle\Entity\Node\Entry The form entry's id.
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="FormBundle\Entity\Node\Entry", cascade={"persist"})
     * @ORM\JoinColumn(name="form_entry_id", referencedColumnName="id")
     */
    private $formEntry;

    /**
     * @var FormBundle\Entity\Field The field this entry is for.
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
     * @param string $label
     */
    public function __construct($formEntry, $field, $value)
    {
        $this->formEntry = $formEntry;
        $this->field = $field;
        $this->value = $value;
    }

    /**
     * @return The form entry this entry belongs to.
     */
    public function getFormEntry() {
        return $this->formEntry;
    }

    /**
     * @return The field this entry belongs to.
     */
    public function getField() {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * @return string
     */
    public function setValue($value) {
        $this->value = $value;
        return $this;
    }

    public function getValueString(Language $language) {
        return $this->getField()->getValueString($language, $this->getValue());
    }
}

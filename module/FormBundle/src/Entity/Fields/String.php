<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
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

namespace FormBundle\Entity\Fields;

use CommonBundle\Entity\General\Language,
    CommonBundle\Entity\Users\Person,
    CommonBundle\Component\Util\Url,
    DateTime,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM,
    FormBundle\Entity\Field;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Fields\String")
 * @ORM\Table(name="forms.field_string")
 */
class String extends Field
{

    /**
     * @var int The maximum length of this string field.
     *
     * @ORM\Column(name="max_length", type="bigint", nullable=true)
     */
    private $maxLength;

    /**
     * @var boolean Whether this is a multiline text field.
     *
     * @ORM\Column(name="multiline", type="boolean")
     */
    private $multiLine;

    /**
     * @param FormBundle\Entity\Nodes\Form $form
     * @param integer $order
     * @param bool $required
     * @param integer $maxLength
     * @param bool $multiLine
     */
    public function __construct($form, $order, $required, $maxLength, $multiLine)
    {
        parent::__construct($form, $order, $required);
        $this->maxLength = $maxLength;
        $this->multiLine = $multiLine;
    }

    /**
     * Returns the maximum length of this string field.
     *
     * @return The maximum length
     */
    public function getMaxLength()
    {
        return $this->maxLength;
    }

    /**
     * Returns whether this is a multiline field.
     *
     * @return True if and only if this is a multiline field.
     */
    public function isMultiLine()
    {
        return $this->multiLine;
    }

    /**
     * Returns whether this field has a maximum length or not.
     *
     * @return True if and only if the maximum length is not null and not zero.
     */
    public function hasMaxLength()
    {
        return $this->maxLength !== NULL && $this->maxLength != 0;
    }

    public function getValueString(Language $language, $value) {
        return $value;
    }

}

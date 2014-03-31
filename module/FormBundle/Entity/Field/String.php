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
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Field\String")
 * @ORM\Table(name="forms.fields_strings")
 */
class String extends Field
{
    /**
     * @var int The maximum length per line of this string field.
     *
     * @ORM\Column(name="line_length", type="bigint", nullable=true)
     */
    private $lineLength;

    /**
     * @var int The maximum number of lines.
     *
     * @ORM\Column(name="lines", type="bigint", nullable=true)
     */
    private $lines;

    /**
     * @var boolean Whether this is a multiline text field.
     *
     * @ORM\Column(name="multiline", type="boolean")
     */
    private $multiLine;

    /**
     * @param Form                     $form
     * @param integer                  $order
     * @param boolean                  $required
     * @param \FormBundle\Entity\Field $visibityDecisionField
     * @param string                   $visibilityValue
     * @param integer                  $lineLength
     * @param integer                  $lines
     * @param boolean                  $multiLine
     */
    public function __construct(Form $form, $order, $required, Field $visibityDecisionField = null, $visibilityValue = null, $lineLength, $lines, $multiLine)
    {
        parent::__construct($form, $order, $required, $visibityDecisionField, $visibilityValue);
        $this->lineLength = $lineLength;
        $this->lines = $lines;
        $this->multiLine = $multiLine;
    }

    /**
     * Returns the maximum number of characters per line for this field.
     *
     * @return integer The maximum number of characters per line.
     */
    public function getLineLength()
    {
        return $this->lineLength;
    }

    /**
     * @param  integer $lineLength The maximum number of characters per line.
     * @return String
     */
    public function setLineLength($lineLength)
    {
        $this->lineLength = $lineLength;

        return $this;
    }

    /**
     * Returns the maximum number of lines for this field.
     *
     * @return integer The maximum number of lines.
     */
    public function getLines()
    {
        if (!$this->isMultiLine())
            return 1;
        return $this->lines;
    }

    /**
     * @param  integer $lines Returns the maximum number of lines for this field.
     * @return String
     */
    public function setLines($lines)
    {
        $this->lines = $lines;

        return $this;
    }

    /**
     * Returns whether this is a multiline field.
     *
     * @return boolean True if and only if this is a multiline field.
     */
    public function isMultiLine()
    {
        return $this->multiLine;
    }

    /**
     * @param  boolean $multiLine Returns whether this is a multiline field.
     * @return String
     */
    public function setMultiLine($multiLine)
    {
        $this->multiLine = $multiLine;

        return $this;
    }

    /**
     * Returns whether this field has a maximum length or not.
     *
     * @return boolean True if and only if the maximum length per line is specified and, for
     *                 multiline fields, the maximum number of lines is specified.
     */
    public function hasLengthSpecification()
    {
        $result = $this->getLineLength() !== NULL && $this->getLineLength() != 0 && $this->getLines() !== NULL && $this->getLines() != 0;

        return $result;
    }

    /**
     * @param  \CommonBundle\Entity\General\Language $language
     * @param  boolean                               $value
     * @return boolean
     */
    public function getValueString(Language $language, $value)
    {
        return $value;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'string';
    }
}

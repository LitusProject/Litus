<?php

namespace FormBundle\Entity\Field;

use CommonBundle\Entity\General\Language;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Field\Text")
 * @ORM\Table(name="form_fields_texts")
 */
class Text extends \FormBundle\Entity\Field
{
    /**
     * @var integer The maximum length per line of this field.
     *
     * @ORM\Column(name="line_length", type="bigint", nullable=true)
     */
    private $lineLength;

    /**
     * @var integer The maximum number of lines.
     *
     * @ORM\Column(name="lines", type="bigint", nullable=true)
     */
    private $lines;

    /**
     * @var boolean Whether this is a multiline field.
     *
     * @ORM\Column(name="multiline", type="boolean")
     */
    private $multiLine;

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
     * @return self
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
        if (!$this->isMultiLine()) {
            return 1;
        }

        return $this->lines;
    }

    /**
     * @param  integer $lines Returns the maximum number of lines for this field.
     * @return self
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
     * @return self
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
        return $this->getLineLength() !== null && $this->getLineLength() != 0 && $this->getLines() !== null && $this->getLines() != 0;
    }

    /**
     * @param  Language $language
     * @param  boolean  $value
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

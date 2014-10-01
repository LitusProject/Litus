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

namespace CommonBundle\Component\Validator;

/**
 * Checks the length of a field, specified by the number of characters per line and the number of lines.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class FieldLineLength extends \Zend\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    /**
     * @var int The maximum length per line
     */
    private $_charsPerLine;

    /**
     * @var int The maximum number of lines
     */
    private $_lines;

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'This field exceeds the maximum character count.',
    );

    /**
     * @param int   $charsPerLine The maximum number of characters per line
     * @param int   $lines        The maximum number of lines
     * @param mixed $opts         The validator's options
     */
    public function __construct($charsPerLine, $lines, $opts = null)
    {
        parent::__construct($opts);

        $this->_charsPerLine = $charsPerLine;
        $this->_lines = $lines;
    }

    /**
     * Returns true if the length doesn't exceed the maximum.
     *
     * @param  string  $value   The value of the field that will be validated
     * @param  array   $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $lines = preg_split('/\r\n|\r|\n/s', $value);

        $len = 0;
        for ($i = count($lines) - 2; $i >= 0; $i--) {
            $line = $lines[$i];
            $len = $len + ceil(strlen($line) === 0 ? 1 : strlen($line) / $this->_charsPerLine) * $this->_charsPerLine;
        }

        $len = $len + strlen($lines[count($lines) - 1]);

        if ($this->_lines * $this->_charsPerLine - $len >= 0) {
            return true;
        }

        $this->error(self::NOT_VALID);

        return false;
    }
}

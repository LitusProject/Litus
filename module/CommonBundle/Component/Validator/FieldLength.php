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
 * Checks the length of a field.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class FieldLength extends \Zend\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    /**
     * @var int The maximum length
     */
    private $_maxLength;

    /**
     * @var int The number of characters counted for a newline
     */
    private $_newlineLength;

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'This field exceeds the maximum character count.'
    );

    /**
     * @param int   $maxLength The maximum length of the value
     * @param int   $newlineLength A newline is interpreted as this number of characters.
     * @param mixed $opts          The validator's options
     */
    public function __construct($maxLength, $newlineLength, $opts = null)
    {
        parent::__construct($opts);

        $this->_maxLength = $maxLength;
        $this->_newlineLength = $newlineLength;
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

        $value = preg_replace('/\r\n|\r|\n/s', str_repeat(' ', $this->_newlineLength), $value);

        if (strlen($value) <= $this->_maxLength)
            return true;

        $this->error(self::NOT_VALID);

        return false;
    }
}

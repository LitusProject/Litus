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

namespace CommonBundle\Component\Validator;

/**
 * Verifies whether the given number is a positive number.
 *
 * @author Lars Vierbergen <vierbergenlars@gmail.com>
 */
class PositiveNumber extends \Zend\Validator\AbstractValidator
{
    const NOT_POSITIVE = 'notPositive';
    const NOT_STRICT_POSITIVE = 'notStrictPositive';

    /**
     * @var bool Strictly positive checking enabled or not.
     */
    private $_strict = null;

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_POSITIVE => 'The value may not be negative',
        self::NOT_STRICT_POSITIVE => 'The value may not be negative or zero'
    );

    /**
     * @param bool $strict Enable striclty positive checking (i.e. zero is not allowed)
     * @param mixed $opts The validator's options
     */
    public function __construct($strict = true, $opts = null)
    {
        $this->_strict = $strict;
        parent::__construct($opts);
    }

    /**
     * Returns true if the value is a positive number.
     *
     * @param string $value The value of the field that will be validated
     * @param array $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $intVal = intval(trim($value), 10);
        if ($intVal > 0)
            return true;

        if ($this->_strict && $intVal == 0)
            $this->error (self::NOT_STRICT_POSITIVE);
        else
            $this->error (self::NOT_POSITIVE);

        return false;
    }
}
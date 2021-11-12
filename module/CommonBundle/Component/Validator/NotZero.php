<?php

namespace CommonBundle\Component\Validator;

/**
 * Matches the field whether it is not zero.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class NotZero extends \CommonBundle\Component\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The value may not be zero',
    );

    /**
     * Returns true if the value is not zero.
     *
     * @param  string     $value   The value of the field that will be validated
     * @param  array|null $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        if (trim($value) != '0' && $value != 0) {
            return true;
        }

        $this->error(self::NOT_VALID);

        return false;
    }
}

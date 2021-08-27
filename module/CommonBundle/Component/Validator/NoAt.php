<?php

namespace CommonBundle\Component\Validator;

/**
 * Validate the university email (no @ sign)
 */
class NoAt extends \CommonBundle\Component\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'This field should not contain an @',
    );

    /**
     * Returns true if and only if a field name has been set, the field name is available in the
     * context, and the value of that field unique and valid.
     *
     * @param  string     $value   The value of the field that will be validated
     * @param  array|null $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $containsAt = strstr($value, '@');

        if ($containsAt) {
            $this->error(self::NOT_VALID);

            return false;
        }

        return true;
    }
}

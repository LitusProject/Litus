<?php

namespace FormBundle\Component\Validator;

/**
 * Checks whether a field may be required or not (visibility)
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class FieldRequired extends \CommonBundle\Component\Validator\AbstractValidator
{
    const MAY_NOT_BE_REQUIRED = 'mayNotBeRequired';

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::MAY_NOT_BE_REQUIRED => 'The field may not be required because it can be invisible',
    );

    /**
     * Returns true if the required field is unchecked or checked if it is allowed
     *
     * @param  string     $value   The value of the field that will be validated
     * @param  array|null $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        if ($value == '1' && isset($context['visible_if']) && $context['visible_if'] != '0') {
            $this->error(self::MAY_NOT_BE_REQUIRED);

            return false;
        }

        return true;
    }
}

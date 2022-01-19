<?php

namespace CommonBundle\Component\Validator;

/**
 * Checks whether a checkbox is true is it is required
 *
 * @author Belian Callaerts <belian.callaerts@vtk.be>
 */
class RequiredCheckbox extends \CommonBundle\Component\Validator\AbstractValidator
{
    const NOT_CHECKED = 'notChecked';

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_CHECKED => 'The checkbox needs to be checked',
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

        if (trim($value) != '0' && $value != 0) {
            return true;
        }

        $this->error(self::NOT_CHECKED);

        return false;
    }
}

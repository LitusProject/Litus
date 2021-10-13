<?php

namespace CommonBundle\Component\Validator;

/**
 * Verifies whether the given number is a positive number.
 *
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 */
class PositiveNumber extends \CommonBundle\Component\Validator\AbstractValidator
{
    const NOT_POSITIVE = 'notPositive';
    const NOT_STRICT_POSITIVE = 'notStrictPositive';

    protected $options = array(
        'strict' => true,
    );

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_POSITIVE        => 'The value may not be negative',
        self::NOT_STRICT_POSITIVE => 'The value may not be negative or zero',
    );

    /**
     * Sets validator options
     *
     * @param integer|array|\Traversable $options
     */
    public function __construct($options = array())
    {
        if (!is_array($options)) {
            $args = func_get_args();
            $options = array();
            $options['strict'] = array_shift($args);
        }

        parent::__construct($options);
    }

    /**
     * Returns true if the value is a positive number.
     *
     * @param  string     $value   The value of the field that will be validated
     * @param  array|null $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $intVal = intval(trim($value), 10);
        if ($intVal > 0) {
            return true;
        }

        if ($this->options['strict'] && $intVal == 0) {
            $this->error(self::NOT_STRICT_POSITIVE);
        } else {
            $this->error(self::NOT_POSITIVE);
        }

        return false;
    }
}

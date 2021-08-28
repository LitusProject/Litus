<?php

namespace CudiBundle\Component\Validator\Sale\Session\Restriction;

/**
 * Check the end value is after the start value
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Values extends \CommonBundle\Component\Validator\AbstractValidator
{
    /**
     * @var string The error codes
     */
    const NOT_VALID = 'notSame';

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The value must be greater than %start_value%',
    );

    /**
     * @var array The message variables
     */
    protected $messageVariables = array(
        'start_value' => array('options' => 'startValue'),
    );

    protected $options = array(
        'start_value' => null,
    );

    /**
     * Original start value against which to validate
     * @var string
     */
    protected $startValue;

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
            $options['start_value'] = array_shift($args);
        }

        parent::__construct($options);
    }

    /**
     * Returns true if and only if a field name has been set, the field name is available in the
     * context, and the value of that field is valid.
     *
     * @param  string     $value   The value of the field that will be validated
     * @param  array|null $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        if ($value === null || $value == '') {
            return true;
        }

        if (($context !== null) && array_key_exists($this->options['start_value'], $context)) {
            $startValue = $context[$this->options['start_value']];
            $this->startValue = $startValue;
        } else {
            $this->error(self::NOT_VALID);

            return false;
        }

        if ($startValue === null) {
            $this->error(self::NOT_VALID);

            return false;
        }

        if ($startValue > $value) {
            $this->error(self::NOT_VALID);

            return false;
        }

        return true;
    }
}

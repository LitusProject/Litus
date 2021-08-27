<?php

namespace CommonBundle\Component\Validator;

/**
 * Checks the length of a field.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class FieldLength extends \CommonBundle\Component\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    protected $options = array(
        'max_length'      => 0,
        'new_line_length' => 0,
    );

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'This field exceeds the maximum character count',
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
            $options['max_length'] = array_shift($args);
            $options['new_line_length'] = array_shift($args);
        }

        parent::__construct($options);
    }

    /**
     * Returns true if the length doesn't exceed the maximum.
     *
     * @param  string     $value   The value of the field that will be validated
     * @param  array|null $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $value = preg_replace('/\r\n|\r|\n/s', str_repeat(' ', $this->options['new_line_length']), $value);

        if (strlen($value) <= $this->options['max_length']) {
            return true;
        }

        $this->error(self::NOT_VALID);

        return false;
    }
}

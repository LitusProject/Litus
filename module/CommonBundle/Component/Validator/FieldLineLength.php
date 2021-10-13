<?php

namespace CommonBundle\Component\Validator;

/**
 * Checks the length of a field, specified by the number of characters per line and the number of lines.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class FieldLineLength extends \CommonBundle\Component\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    protected $options = array(
        'chars_per_line' => 0,
        'lines'          => 0,
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
            $options['chars_per_line'] = array_shift($args);
            $options['lines'] = array_shift($args);
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

        $lines = preg_split('/\r\n|\r|\n/s', $value);

        $len = 0;
        for ($i = count($lines) - 2; $i >= 0; $i--) {
            $line = $lines[$i];
            $len += ceil(strlen($line) === 0 ? 1 : strlen($line) / $this->options['chars_per_line']) * $this->options['chars_per_line'];
        }

        $len += strlen($lines[count($lines) - 1]);

        if ($this->options['lines'] * $this->options['chars_per_line'] - $len >= 0) {
            return true;
        }

        $this->error(self::NOT_VALID);

        return false;
    }
}

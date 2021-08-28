<?php

namespace FormBundle\Component\Validator;

/**
 * Checks whether the text field was specified correctly.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class TextField extends \CommonBundle\Component\Validator\AbstractValidator
{
    const ML_BOTH = 'mlboth';
    const NON_ML_LINES = 'nonmllines';

    protected $options = array(
        'multiline' => false,
        'lines'     => 0,
    );

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::ML_BOTH      => 'Multiline fields must specify either both a line limit and a character limit or none',
        self::NON_ML_LINES => 'Non multiline fields should not specify a maximum number of lines',
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
            $options['multiline'] = array_shift($args);
            $options['lines'] = array_shift($args);
        }

        parent::__construct($options);
    }

    /**
     * Returns true if a person exists for this value, but no driver exists for that person.
     *
     * @param  string     $value   The value of the field that will be validated
     * @param  array|null $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        if ($this->options['multiline']) {
            if (($this->isSpecified($this->options['lines']) && !$this->isSpecified($value))
                || (!$this->isSpecified($this->options['lines']) && $this->isSpecified($value))
            ) {
                $this->error(self::ML_BOTH);

                return false;
            }
        } else {
            if ($this->isSpecified($this->options['lines'])) {
                $this->error(self::NON_ML_LINES);

                return false;
            }
        }

        return true;
    }

    /**
     * Checks whether the given value is a non-zero character count or line count specification.
     *
     * @param mixed $value The value to check.
     */
    private function isSpecified($value)
    {
        return $value !== null && $value != 0 && $value != '';
    }
}

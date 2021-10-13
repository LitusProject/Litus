<?php

namespace CommonBundle\Component\Validator;

use DateTime;

/**
 * Matches the given faq title against the database to check whether it is
 * unique or not.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class DateCompare extends \CommonBundle\Component\Validator\AbstractValidator
{
    /**
     * @var string The error codes
     */
    const NOT_VALID = 'notSame';

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The date must be after %first_date%',
    );

    /**
     * @var array The message variables
     */
    protected $messageVariables = array(
        'first_date' => array('options' => 'first_date'),
    );

    protected $options = array(
        'first_date' => '',
        'format'     => '',
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
            $options['first_date'] = array_shift($args);
            $options['format'] = array_shift($args);
        }

        parent::__construct($options);
    }

    /**
     * Returns true if and only if the end date is after the start date
     *
     * @param  mixed      $value
     * @param  array|null $context
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        if ($value === null || $value == '') {
            return true;
        }

        if ($this->options['first_date'] == 'now') {
            $endDate = 'now';
        } else {
            $endDate = self::getFormValue($context, $this->options['first_date']);
            if ($endDate === null) {
                $this->error(self::NOT_VALID);

                return false;
            }
        }

        if (DateTime::createFromFormat($this->options['format'], $value) <= DateTime::createFromFormat($this->options['format'], $endDate)) {
            $this->error(self::NOT_VALID);

            return false;
        }

        return true;
    }
}

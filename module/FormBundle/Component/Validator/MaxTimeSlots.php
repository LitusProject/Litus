<?php

namespace FormBundle\Component\Validator;

/**
 * Matches the timeslot for occupation of user
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class MaxTimeSlots extends \CommonBundle\Component\Validator\AbstractValidator
{
    /**
     * @var string The error codes
     */
    const TOO_MANY = 'tooMany';

    protected $options = array(
        'form' => null,
    );

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::TOO_MANY => 'Too many time slots were selected',
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
            $options['form'] = array_shift($args);
        }

        parent::__construct($options);
    }

    /**
     * Returns true if and only if the end date is after the start date
     *
     * @param  string     $value   The value of the field that will be validated
     * @param  array|null $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $valid = true;

        $num = 0;

        foreach ($this->options['form']->getFields() as $field) {
            $num += isset($context['field-' . $field->getId()]) && $context['field-' . $field->getId()];
        }

        if ($num > 1 && !$this->options['form']->isMultiple()) {
            $this->error(self::TOO_MANY);
            $valid = false;
        }

        return $valid;
    }
}

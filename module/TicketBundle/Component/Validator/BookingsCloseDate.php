<?php

namespace TicketBundle\Component\Validator;

use DateTime;

/**
 * Check the bookings close date is not after the event's date.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class BookingsCloseDate extends \CommonBundle\Component\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    protected $options = array(
        'format' => '',
    );

    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The bookings close date cannot be after the event',
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
            $options['format'] = array_shift($args);
        }

        parent::__construct($options);
    }

    /**
     * Returns true if these does not exceed max
     *
     * @param  string     $value   The value of the field that will be validated
     * @param  array|null $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        if (!is_numeric($context['event'])) {
            return false;
        }

        $event = $this->getEntityManager()
            ->getRepository('CalendarBundle\Entity\Node\Event')
            ->findOneById($context['event']);

        if ($event === null || $event->getStartDate() >= DateTime::createFromFormat($this->options['format'], $value)) {
            return true;
        }

        $this->error(self::NOT_VALID);

        return false;
    }
}

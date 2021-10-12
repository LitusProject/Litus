<?php

namespace TicketBundle\Component\Validator;

use TicketBundle\Entity\Event;

/**
 * Check the activity has already a ticket system
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Activity extends \CommonBundle\Component\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    protected $options = array(
        'exclude' => null,
    );

    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The activity already has a ticket system',
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
            $options['exclude'] = array_shift($args);
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

        $activity = $this->getEntityManager()
            ->getRepository('CalendarBundle\Entity\Node\Event')
            ->findOneById($value);

        $event = $this->getEntityManager()
            ->getRepository('TicketBundle\Entity\Event')
            ->findOneByActivity($activity);

        if ($event === null || ($this->options['exclude'] !== null && $event->getId() == $this->options['exclude']->getId())) {
            return true;
        }

        $this->error(self::NOT_VALID);

        return false;
    }
}

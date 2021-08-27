<?php

namespace PromBundle\Component\Validator;

use PromBundle\Entity\Bus;

/**
 * Checking there are enough seats on the bus
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class BusSeats extends \CommonBundle\Component\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    /**
     * @var Bus
     */
    private $bus;

    protected $options = array(
        'bus' => null,
    );

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'There are no seats left in on this bus you selected.',
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
            $options['bus'] = array_shift($args);
        }

        if (isset($options['bus']) && $options['bus'] instanceof Bus) {
            $this->bus = $options['bus'];
        }

        parent::__construct($options);
    }

    /**
     * Returns true if no matching record is found in the database.
     *
     * @param  string     $value   The value of the field that will be validated
     * @param  array|null $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $newBus = $this->getEntityManager()
            ->getRepository('PromBundle\Entity\Bus')
            ->findOneById($value);

        if ($this->bus == $newBus || $newBus === null) {
            return true;
        }

        if ($newBus->getTotalSeats() - $newBus->getReservedSeats() > 0) {
            return true;
        }

        $this->error(self::NOT_VALID);

        return false;
    }
}

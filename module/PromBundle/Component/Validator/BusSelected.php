<?php

namespace PromBundle\Component\Validator;

/**
 * Checking at least one bus is selected
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class BusSelected extends \CommonBundle\Component\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    protected $options = array(
        'exclude' => null,
    );

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'Please select at least one bus.',
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
     * Returns true if no matching record is found in the database.
     *
     * @param  string     $value   The value of the field that will be validated
     * @param  array|null $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $firstBus = $this->getEntityManager()
            ->getRepository('PromBundle\Entity\Bus')
            ->findOneById($context['first_bus']);

        $secondBus = $this->getEntityManager()
            ->getRepository('PromBundle\Entity\Bus')
            ->findOneById($context['second_bus']);

        if ($firstBus !== null || $secondBus !== null) {
            return true;
        }

        $this->error(self::NOT_VALID);

        return false;
    }
}

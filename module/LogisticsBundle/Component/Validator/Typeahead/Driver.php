<?php

namespace LogisticsBundle\Component\Validator\Typeahead;

/**
 * Checks whether a user exists with the given name or id and whether
 * no driver is created for this user yet.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Driver extends \CommonBundle\Component\Validator\Typeahead\Person
{
    const DRIVER_EXISTS = 'driverExists';

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID     => 'This person does not exist',
        self::DRIVER_EXISTS => 'A driver already exists for that user',
    );

    /**
     * Returns true if a person exists for this value, but no driver exists for that person.
     *
     * @param  string     $value   The value of the field that will be validated
     * @param  array|null $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        if (!parent::isValid($value, $context)) {
            return false;
        }

        $person = $this->getEntityObject();

        $driver = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Driver')
            ->findOneById($person->getId());

        if ($driver !== null) {
            $this->error(self::DRIVER_EXISTS);

            return false;
        }

        return true;
    }
}

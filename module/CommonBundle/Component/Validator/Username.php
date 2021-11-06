<?php

namespace CommonBundle\Component\Validator;

/**
 * Matches the given username against the database to check whether it is
 * unique or not.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Username extends \CommonBundle\Component\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The username already exists',
    );

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

        $person = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person')
            ->findOneByUsername($value);

        if ($person === null) {
            return true;
        }

        $this->error(self::NOT_VALID);

        return false;
    }
}

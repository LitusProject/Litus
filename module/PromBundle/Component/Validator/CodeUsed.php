<?php

namespace PromBundle\Component\Validator;

/**
 * Matches the given code title against the database to check whether it is
 * used or not.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class CodeUsed extends \CommonBundle\Component\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    protected $options = array(
        'exclude' => null,
    );

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The code you entered is not valid.',
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

        $code = $this->getEntityManager()
            ->getRepository('PromBundle\Entity\Bus\ReservationCode')
            ->getRegistrationCodeByCode($value);

        if ($code->getAcademicYear() != $this->getCurrentAcademicYear()) {
            return false;
        }

        if ($code === null) {
            return false;
        }

        $passenger = $this->getEntityManager()
            ->getRepository('PromBundle\Entity\Bus\Passenger')
            ->findPassengerByCode($code);

        if (!$code->isUsed() && !isset($passenger[0])) {
            return true;
        }

        $this->error(self::NOT_VALID);

        return false;
    }
}

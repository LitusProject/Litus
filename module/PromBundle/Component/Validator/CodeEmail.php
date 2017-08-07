<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace PromBundle\Component\Validator;

/**
 * Matches the given code title against the database to check whether it is
 * the email is linked to the code
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class CodeEmail extends \CommonBundle\Component\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    protected $options = array(
        'exclude' => null,
    );

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The combination of email and code you entered does not exist.',
    );

    /**
     * Sets validator options
     *
     * @param int|array|\Traversable $options
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
            ->getRegistrationCodeByCode($context['ticket_code']);

        if (null === $code) {
            return false;
        }

        $passengers = $this->getEntityManager()
            ->getRepository('PromBundle\Entity\Bus\Passenger')
            ->findPassengerByCode($code);

        $passenger = null;
        if (sizeof($passengers) > 0) {
            $passenger = $passengers[0];
        }

        if (null !== $passenger && $value == $passenger->getEmail()) {
            return true;
        }

        $this->error(self::NOT_VALID);

        return false;
    }
}

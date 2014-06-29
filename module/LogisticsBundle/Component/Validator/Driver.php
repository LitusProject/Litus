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
 *
 * @license http://litus.cc/LICENSE
 */

namespace LogisticsBundle\Component\Validator;

use Doctrine\ORM\EntityManager;

/**
 * Checks whether a user exists with the given name or id and whether
 * no driver is created for this user yet.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Driver extends \CommonBundle\Component\Validator\Academic
{
    const DRIVER_EXISTS = 'driverExists';

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NO_SUCH_USER => 'The user doesn\'t exist',
        self::DRIVER_EXISTS => 'A driver already exists for that user',
    );

    /**
     * Returns true if a person exists for this value, but no driver exists for that person.
     *
     * @param  string  $value   The value of the field that will be validated
     * @param  array   $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        if (!parent::isValid($value, $context)) {
            return false;
        }

        $person = $this->getPerson($value);

        $driver = $this->_entityManager
            ->getRepository('LogisticsBundle\Entity\Driver')
            ->findOneById($person->getId());

        if (null !== $driver) {
            $this->error(self::DRIVER_EXISTS);

            return false;
        }

        return true;
    }
}

<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
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
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param mixed $opts The validator's options. An additional option 'byId' can be set
     *                     to indicate whether a user id or user name is validated. By default
     *                     this is false, indicating search by user name.
     */
    public function __construct(EntityManager $entityManager, $opts = null)
    {
        parent::__construct($entityManager, $opts);
    }

    /**
     * Returns true if a person exists for this value, but no driver exists for that person.
     *
     * @param string $value The value of the field that will be validated
     * @param array $context The context of the field that will be validated
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

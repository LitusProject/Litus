<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
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
class Driver extends \Zend\Validator\AbstractValidator
{
    const NO_SUCH_USER = 'noUser';
    const DRIVER_EXISTS = 'driverExists';

    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;
    
    /**
     * @var Boolean Indicates whether the user should be searched by id or by name.
     */
    private $_byId = false;

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
        parent::__construct($opts);

        $this->_entityManager = $entityManager;
        $this->_byId = $opts['byId'];
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
        $this->setValue($value);

        $repository = $this->_entityManager
            ->getRepository('CommonBundle\Entity\Users\People\Academic');
        
        if ($this->_byId) {
            $person = $repository->findOneById($value);
        } else {
            $person = $repository->findOneByUsername($value);
        }
        
        if (null === $person) {
            $this->error(self::NO_SUCH_USER);
            return false;
        }
        
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

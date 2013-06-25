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

namespace CommonBundle\Component\Validator;

use Doctrine\ORM\EntityManager;

/**
 * Checks whether a user exists with the given name or id.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Academic extends \Zend\Validator\AbstractValidator
{
    const NO_SUCH_USER = 'noUser';

    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @var Boolean Indicates whether the user should be searched by id or by name.
     */
    protected $_byId = false;

    /**
     * @var Boolean Indicates whether an empty id or name should be accepted.
     */
    protected $_isRequired = false;

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NO_SUCH_USER => 'The user doesn\'t exist',
    );

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param mixed $opts The validator's options. The following additional options are available:
     *                     'byId' can be set to indicate whether a user id or user name is validated.
     *                     By default this is false, indicating search by user name.
     *                     'isRequired' is false by default. True indicates that empty values are not accepted.
     */
    public function __construct(EntityManager $entityManager, $opts = null)
    {
        parent::__construct($opts);

        $this->_entityManager = $entityManager;
        $this->_byId = isset($opts['byId']) && $opts['byId'];
        $this->_isRequired = isset($opts['isRequired']) && $opts['isRequired'];
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
        if (!$this->_isRequired && '' == $value)
            return true;

        $this->setValue($value);

        $person = $this->getPerson($value);

        if (null === $person) {
            $this->error(self::NO_SUCH_USER);
            return false;
        }

        return true;
    }

    protected function getPerson($value)
    {
        $repository = $this->_entityManager
        ->getRepository('CommonBundle\Entity\User\Person\Academic');

        if ($this->_byId) {
            $person = $repository->findOneById($value);
        } else {
            $person = $repository->findOneByUsername($value);
        }

        return $person;
    }
}

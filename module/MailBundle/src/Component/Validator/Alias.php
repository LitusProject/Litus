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
 *
 * @license http://litus.cc/LICENSE
 */

namespace MailBundle\Component\Validator;

use CommonBundle\Component\Util\Url,
    Doctrine\ORM\EntityManager;

/**
 * Checks whether an alias is unique or not.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Alias extends \Zend\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @var string The alias exluded from this check
     */
    private $_alias;

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'An alias with this name already exists'
    );

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param string The alias exluded from this check
     * @param mixed $opts The validator's options
     */
    public function __construct(EntityManager $entityManager, $alias = null, $opts = null)
    {
        parent::__construct($opts);

        $this->_entityManager = $entityManager;
        $this->_alias = $alias;
    }

    /**
     * Returns true if no matching record is found in the database.
     *
     * @param string $value The value of the field that will be validated
     * @param array $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $alias = $this->_entityManager
            ->getRepository('MailBundle\Entity\Alias')
            ->findOneByName($value);

        if (null === $alias || ($this->_alias && $alias == $this->_alias))
            return true;

        $this->error(self::NOT_VALID);

        return false;
    }
}

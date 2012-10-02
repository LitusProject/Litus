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

namespace SyllabusBundle\Component\Validator\Department;

use Doctrine\ORM\EntityManager,
    SyllabusBundle\Entity\Department;

/**
 * Matches the given department name against the database to check whether it is
 * unique or not.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Name extends \Zend\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @var \SyllabusBundle\Entity\Department The deparment exluded from this check
     */
    private $_exclude = '';

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'There already exists a department with this name'
    );

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param string The name exluded from this check
     * @param mixed $opts The validator's options
     */
    public function __construct(EntityManager $entityManager, Department $exclude = null, $opts = null)
    {
        parent::__construct($opts);

        $this->_entityManager = $entityManager;
        $this->_exclude = $exclude;
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

        $department = $this->_entityManager
            ->getRepository('SyllabusBundle\Entity\Department')
            ->findOneByName($value);

        if (null === $department || ($this->_exclude !== null && $department->getId() == $this->_exclude->getId()))
            return true;

        $this->error(self::NOT_VALID);

        return false;
    }
}

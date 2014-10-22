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

namespace CommonBundle\Component\Validator;

use Doctrine\ORM\EntityManager,
    RuntimeException;

abstract class Typeahead extends \Zend\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    /**
     * @var EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @var string The entity to check
     */
    protected $entity;

    /**
     * @var mixed The found entity
     */
    protected $entityObject;

    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'This entity does not exits',
    );

    /**
     * Create a new typeahead validator
     *
     * @param EntityManager $entityManager The EntityManager instance
     * @param mixed         $opts          The validator's options
     */
    public function __construct(EntityManager $entityManager, $opts = null)
    {
        parent::__construct($opts);

        $this->_entityManager = $entityManager;

        if (empty($this->entity)) {
            throw new RuntimeException('The typeahead validator needs an entity');
        }
    }

    /**
     * Returns true if and only if a field name has been set, the field name is available in the
     * context, and the value of that field unique and valid.
     *
     * @param  string  $value   The value of the field that will be validated
     * @param  array   $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        if (null == $context['id']) {
            $this->error(self::NOT_VALID);

            return false;
        }

        $this->entityObject = $this->_entityManager
            ->getRepository($this->entity)
            ->findOneById($context['id']);

        if (null === $this->entityObject) {
            $this->error(self::NOT_VALID);

            return false;
        }

        return true;
    }

    /**
     * @return mixed
     */
    public function getEntityObject()
    {
        return $this->entityObject;
    }
}

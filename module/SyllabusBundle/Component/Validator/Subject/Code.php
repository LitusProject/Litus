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

namespace SyllabusBundle\Component\Validator\Subject;

use Doctrine\ORM\EntityManager,
    SyllabusBundle\Entity\Subject;

/**
 * Matches the given subject code against the database to check whether it exists or not.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Code extends \Zend\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @var \SyllabusBundle\Entity\Subject The subject exluded from this check
     */
    private $_exclude;

    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The subject code does already exist'
    );

    /**
     * Create a new Article Barcode validator.
     *
     * @param \Doctrine\ORM\EntityManager    $entityManager The EntityManager instance
     * @param \SyllabusBundle\Entity\Subject $exclude
     * @param mixed                          $opts          The validator's options
     */
    public function __construct(EntityManager $entityManager, Subject $exclude = null, $opts = null)
    {
        parent::__construct($opts);

        $this->_entityManager = $entityManager;
        $this->_exclude = $exclude;
    }


    /**
     * Returns true if and only if a field name has been set, the field name is available in the
     * context, and the value of that field is valid.
     *
     * @param  string  $value   The value of the field that will be validated
     * @param  array   $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $subject = $this->_entityManager
            ->getRepository('SyllabusBundle\Entity\Subject')
            ->findOneByCode($value);

        if (null === $subject || ($this->_exclude !== null && $subject->getId() == $this->_exclude->getId()))
            return true;

        $this->error(self::NOT_VALID);

        return false;
    }
}

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

use CommonBundle\Entity\General\AcademicYear,
    Doctrine\ORM\EntityManager,
    SyllabusBundle\Entity\Subject;

/**
 * Matches the given subject against the database to check duplicate mappings.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Study extends \Zend\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    /**
     * @var EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @var Subject The subject
     */
    private $_subject;

    /**
     * @var AcademicYear The academic year
     */
    private $_academicYear;

    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The mapping already exists'
    );

    /**
     * Create a new Article Barcode validator.
     *
     * @param EntityManager $entityManager The EntityManager instance
     * @param Subject       $subject
     * @param AcademicYear  $academicYear
     * @param mixed         $opts          The validator's options
     */
    public function __construct(EntityManager $entityManager, Subject $subject = null, AcademicYear $academicYear, $opts = null)
    {
        parent::__construct($opts);

        $this->_entityManager = $entityManager;
        $this->_subject = $subject;
        $this->_academicYear = $academicYear;
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

        $study = $this->_entityManager
            ->getRepository('SyllabusBundle\Entity\Study')
            ->findOneById($context['study_id']);

        $mapping = $this->_entityManager
            ->getRepository('SyllabusBundle\Entity\StudySubjectMap')
            ->findOneByStudySubjectAndAcademicYear($study, $this->_subject, $this->_academicYear);

        if (null === $mapping)
            return true;

        $this->error(self::NOT_VALID);

        return false;
    }
}

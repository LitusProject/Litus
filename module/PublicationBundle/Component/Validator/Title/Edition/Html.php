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

namespace PublicationBundle\Component\Validator\Title\Edition;

use CommonBundle\Entity\General\AcademicYear,
    Doctrine\ORM\EntityManager,
    PublicationBundle\Entity\Publication;

/**
 * Checks whether a publication title already exists.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Html extends \Zend\Validator\AbstractValidator
{
    const TITLE_EXISTS = 'titleExists';

    /**
     * @var EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @var integer The ID to ignore
     */
    private $_id;

    /**
     * @var Publication The publication
     */
    private $_publication;

    /**
     * @var AcademicYear The year
     */
    private $_academicYear;

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::TITLE_EXISTS => 'There already is a HTML edition with this title for this publication',
    );

    /**
     * @param EntityManager $entityManager The EntityManager instance
     * @param Publication   $publication
     * @param Academicyear  $academicYear
     * @param integer       $id            The ID that should be ignored when checking for duplicate titles.
     * @param mixed         $opts          The validator's options.
     */
    public function __construct(EntityManager $entityManager, Publication $publication, Academicyear $academicYear, $id = null, $opts = array())
    {
        parent::__construct($opts);

        $this->_entityManager = $entityManager;
        $this->_id = $id;
        $this->_publication = $publication;
        $this->_academicYear = $academicYear;
    }

    /**
     * Returns true if no edition with this title exists.
     *
     * @param  string  $value   The value of the field that will be validated
     * @param  array   $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $edition = $this->_entityManager
            ->getRepository('PublicationBundle\Entity\Edition\Html')
            ->findOneByPublicationTitleAndAcademicYear($this->_publication, $value, $this->_academicYear);

        if (null !== $edition) {
            if (null === $this->_id || $edition->getId() !== $this->_id) {
                $this->error(self::TITLE_EXISTS);

                return false;
            }
        }

        return true;
    }
}

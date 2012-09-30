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

namespace PublicationBundle\Component\Validator;

use CommonBundle\Entity\General\AcademicYear,
    Doctrine\ORM\EntityManager,
    PublicationBundle\Entity\Publication;

/**
 * Checks whether a publication title already exists.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class HtmlEditionTitleValidator extends \Zend\Validator\AbstractValidator
{
    const TITLE_EXISTS = 'titleExists';

    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @var int The id to ignore.
     */
    private $_id;

    /**
     * @var PublicationBundle\Entity\Publication The publication
     */
    private $_publication;

    /**
     * @var CommonBundle\Entity\General\AcademicYear The year
     */
    private $_academicYear;

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::TITLE_EXISTS => 'There is a html edition with this title already for this publication!',
    );

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param $id The id that should be ignored when checking for duplicate titles.
     * @param mixed $opts The validator's options.
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
     * @param string $value The value of the field that will be validated
     * @param array $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {

        $edition = $this->_entityManager
            ->getRepository('PublicationBundle\Entity\Editions\Html')
            ->findOneByPublicationTitleAndAcademicYear($this->_publication, $value, $this->_academicYear);

        if ($edition) {
            if ($this->_id === null || $edition->getId() !== $this->_id) {
                $this->error(self::TITLE_EXISTS);
                return false;
            }
        }

        return true;
    }
}

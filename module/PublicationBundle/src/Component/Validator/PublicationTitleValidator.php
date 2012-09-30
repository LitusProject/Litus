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

use Doctrine\ORM\EntityManager;

/**
 * Checks whether a publication title already exists.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class PublicationTitleValidator extends \Zend\Validator\AbstractValidator
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
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::TITLE_EXISTS => 'There is a publication with this title already!',
    );

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param $id The id that should be ignored when checking for duplicate titles.
     * @param mixed $opts The validator's options.
     */
    public function __construct(EntityManager $entityManager, $id = null, $opts = array())
    {
        parent::__construct($opts);

        $this->_entityManager = $entityManager;
        $this->_id = $id;
    }

    /**
     * Returns true if no publication with this title exists.
     *
     * @param string $value The value of the field that will be validated
     * @param array $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {

        $publication = $this->_entityManager
            ->getRepository('PublicationBundle\Entity\Publication')
            ->findOneByTitle($value);

        if ($publication) {
            if ($this->_id === null || $publication->getId() !== $this->_id) {
                $this->error(self::TITLE_EXISTS);
                return false;
            }
        }

        return true;
    }
}

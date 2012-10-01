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

namespace OnBundle\Component\Validator;

use Doctrine\ODM\MongoDB\DocumentManager;

/**
 * Checks whether a slug name already exists.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Name extends \Zend\Validator\AbstractValidator
{
    const TITLE_EXISTS = 'nameExists';

    /**
     * @var \Doctrine\ODM\MongoDB\DocumentManager The DocumentManager instance
     */
    protected $_documentManager = null;

    /**
     * @var int The ID to ignore.
     */
    private $_id;

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::TITLE_EXISTS => 'There is a publication with this title already!',
    );

    /**
     * @param \Doctrine\ODM\MongoDB\DocumentManager $documentManager The DocumentManager instance
     * @param $id The ID that should be ignored when checking for duplicate names
     * @param mixed $opts The validator's options.
     */
    public function __construct(DocumentManager $documentManager, $id = null, $opts = array())
    {
        parent::__construct($opts);

        $this->_documentManager = $documentManager;
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

        $slug = $this->_documentManager
            ->getRepository('OnBundle\Document\Slug')
            ->findOneByName($value);

        if (null !== $slug) {
            if (null === $this->_id || $slug->getId() !== $this->_id) {
                $this->error(self::TITLE_EXISTS);
                return false;
            }
        }

        return true;
    }
}

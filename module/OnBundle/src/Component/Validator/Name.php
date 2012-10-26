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
     * @var int The slug to ignore
     */
    private $_slug;

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::TITLE_EXISTS => 'There already is a slug with this title',
    );

    /**
     * @param \Doctrine\ODM\MongoDB\DocumentManager $documentManager The DocumentManager instance
     * @param \OnBundle\Document\Slug $slug The slug that should be ignored when checking for duplicate names
     * @param mixed $opts The validator's options.
     */
    public function __construct(DocumentManager $documentManager, $slug = null, $opts = array())
    {
        parent::__construct($opts);

        $this->_documentManager = $documentManager;
        $this->_slug = $slug;
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

        if (null === $slug || ($this->_slug && $slug == $this->_slug))
            return true;

        $this->error(self::TITLE_EXISTS);
        return false;
    }
}

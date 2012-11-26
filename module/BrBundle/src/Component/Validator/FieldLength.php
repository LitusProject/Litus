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

namespace BrBundle\Component\Validator;

use CommonBundle\Component\Util\Url,
    Doctrine\ORM\EntityManager,
    BrBundle\Entity\Company;

/**
 * Checks the length of a field.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class FieldLength extends \Zend\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    /**
     * @var The maximum length
     */
    private $_maxLength;

    /**
     * @var The number of characters counted for a newline
     */
    private $_newlineLength;

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'This field exceeds the maximum character count.'
    );

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \BrBundle\Entity\Company The company exluded from this check
     * @param mixed $opts The validator's options
     */
    public function __construct($maxLength, $newlineLength, $opts = null)
    {
        parent::__construct($opts);

        $this->_maxLength = $maxLength;
        $this->_newlineLength = $newlineLength;
    }

    /**
     * Returns true if the length doesn't exceed the maximum.
     *
     * @param string $value The value of the field that will be validated
     * @param array $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $value = preg_replace('/\r\n|\r|\n/s', str_repeat(' ', $this->_newlineLength), $value);

        if (strlen($value) <= $this->_maxLength)
            return true;

        $this->error(self::NOT_VALID);

        return false;
    }
}

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

namespace FormBundle\Component\Validator;

use Doctrine\ORM\EntityManager;

/**
 * Checks whether a field may be required or not (visibility)
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Required extends \Zend\Validator\AbstractValidator
{
    const MAY_NOT_BE_REQUIRED = 'mayNotBeRequired';

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::MAY_NOT_BE_REQUIRED => 'The field may not be required because it can be invisible',
    );

    /**
     * @param mixed $opts The validator's options
     */
    public function __construct($opts = null)
    {
        parent::__construct($opts);
    }

    /**
     * Returns true if the required field is unchecked or checked if it is allowed
     *
     * @param string $value The value of the field that will be validated
     * @param array $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        if ($value == '1' && isset($context['visible_if']) && $context['visible_if'] !== '0') {
            $this->error(self::MAY_NOT_BE_REQUIRED);
            return false;
        }

        return true;
    }
}
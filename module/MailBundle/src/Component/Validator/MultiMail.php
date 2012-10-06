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

namespace MailBundle\Component\Validator;

use Zend\Validator\EmailAddress as EmailAddressValidator;

/**
 * Validates multiple email addresses separated by commas or whitespaces.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class MultiMail extends \Zend\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The email addresses should be valid and separated by commas or whitespaces.'
    );

    /**
     * @param mixed $opts The validator's options
     */
    public function __construct($opts = null)
    {
        parent::__construct($opts);
    }

    /**
     * Returns true if no matching record is found in the database.
     *
     * @param string $value The value of the field that will be validated
     * @param array $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $validator = new EmailAddressValidator();

        $addresses = preg_split("/[,;\s]+/", $value);

        foreach ($addresses as $address) {
            if (!$validator->isValid($address)) {
                $this->error(self::NOT_VALID);
                return false;
            }
        }

        return true;
    }
}

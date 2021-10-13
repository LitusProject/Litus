<?php

namespace MailBundle\Component\Validator;

use Laminas\Validator\EmailAddress as EmailAddressValidator;

/**
 * Validates multiple email addresses separated by commas or whitespaces.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class MultiMail extends \CommonBundle\Component\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The email addresses should be valid and separated by commas or whitespaces',
    );

    /**
     * Returns true if no matching record is found in the database.
     *
     * @param  string     $value   The value of the field that will be validated
     * @param  array|null $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $validator = new EmailAddressValidator();

        $addresses = preg_split('/[,;\s]+/', $value);

        foreach ($addresses as $address) {
            if (!$validator->isValid($address)) {
                $this->error(self::NOT_VALID);

                return false;
            }
        }

        return true;
    }
}

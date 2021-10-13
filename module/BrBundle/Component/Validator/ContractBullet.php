<?php

namespace BrBundle\Component\Validator;

use BrBundle\Component\ContractParser\IllegalFormatException;
use BrBundle\Component\ContractParser\Parser as BulletParser;

/**
 * Check for syntac errors in text.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ContractBullet extends \CommonBundle\Component\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The text cannot be parsed',
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

        try {
            $p = new BulletParser();
            $p->parse($value);
        } catch (IllegalFormatException $e) {
            $this->error(self::NOT_VALID);

            return false;
        }

        return true;
    }
}

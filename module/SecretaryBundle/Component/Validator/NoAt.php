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

namespace SecretaryBundle\Component\Validator;

use CommonBundle\Entity\User\Person,
    Doctrine\ORM\EntityManager;

class NoAt extends \Zend\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @var \CommonBundle\Entity\User\Person
     */
    private $_person = null;

    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'This field should not contain an @',
    );

    /**
     * Create a new Unique Article Barcode validator.
     *
     * @param mixed $opts The validator's options
     */
    public function __construct($opts = null)
    {
        parent::__construct($opts);
    }


    /**
     * Returns true if and only if a field name has been set, the field name is available in the
     * context, and the value of that field unique and valid.
     *
     * @param  string  $value   The value of the field that will be validated
     * @param  array   $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $containsAt = strstr($value, '@');

        if ($containsAt) {
            $this->error(self::NOT_VALID);

            return false;
        }

        return true;
    }
}

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

namespace CommonBundle\Component\Validator\Person;

use Doctrine\ORM\EntityManager;

class Typeahead extends \Zend\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    /**
     * @var EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The person does not exits',
    );

    /**
     * Create a new person typeahead validator
     *
     * @param EntityManager $entityManager The EntityManager instance
     * @param mixed         $opts          The validator's options
     */
    public function __construct(EntityManager $entityManager, $opts = null)
    {
        parent::__construct($opts);

        $this->_entityManager = $entityManager;
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
        $id = null;
        foreach ($context as $key => $value) {
            if (strpos($key, '_id') > 0) {
                $id = $value;
            }
        }

        if (null == $id) {
            echo 'no Id';
            $this->error(self::NOT_VALID);

            return false;
        }

        $person = $this->_entityManager
            ->getRepository('CommonBundle\Entity\User\Person')
            ->findOneById($id);

        if (null === $person) {
            $this->error(self::NOT_VALID);

            return false;
        }

        return true;
    }
}

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

use CommonBundle\Entity\User\Person,
    Doctrine\ORM\EntityManager;

class Barcode extends \Zend\Validator\AbstractValidator
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
        self::NOT_VALID => 'The academic barcode already exists'
    );

    /**
     * Create a new Unique Article Barcode validator.
     *
     * @param \Doctrine\ORM\EntityManager      $entityManager The EntityManager instance
     * @param \CommonBundle\Entity\User\Person $person
     * @param mixed                            $opts          The validator's options
     */
    public function __construct(EntityManager $entityManager, Person $person = null, $opts = null)
    {
        parent::__construct($opts);

        $this->_entityManager = $entityManager;
        $this->_person = $person;
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
        $this->setValue($value);

        if (! is_numeric($value)) {
            $this->error(self::NOT_VALID);

            return false;
        }

        if (null === $this->_person)
            return true;

        $barcode = $this->_entityManager
            ->getRepository('CommonBundle\Entity\User\Barcode')
            ->findOneByBarcode($value);

        if (null === $barcode || ($this->_person && $barcode->getPerson() == $this->_person))
            return true;

        $this->error(self::NOT_VALID);

        return false;
    }
}

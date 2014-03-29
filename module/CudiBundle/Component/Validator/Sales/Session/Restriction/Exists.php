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

namespace CudiBundle\Component\Validator\Sales\Session\Restriction;

use CudiBundle\Entity\Sale\Session,
    Doctrine\ORM\EntityManager;

/**
 * Check Restriction already exists.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Exists extends \Zend\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @var \CudiBundle\Entity\Sale\Session
     */
    private $_session = null;

    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The restriction already exists'
    );

    /**
     * Create a new Article Barcode validator.
     *
     * @param \Doctrine\ORM\EntityManager     $entityManager The EntityManager instance
     * @param \CudiBundle\Entity\Sale\Session $session
     * @param mixed                           $opts          The validator's options
     */
    public function __construct(EntityManager $entityManager, Session $session, $opts = null)
    {
        parent::__construct($opts);

        $this->_entityManager = $entityManager;
        $this->_session = $session;
    }


    /**
     * Returns true if and only if a field name has been set, the field name is available in the
     * context, and the value of that field is valid.
     *
     * @param  string  $value   The value of the field that will be validated
     * @param  array   $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $restriction = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sale\Session\Restriction')
            ->findOneBySessionAndType($this->_session, $value);

        if (null == $restriction)
            return true;

        $this->error(self::NOT_VALID);

        return false;
    }
}

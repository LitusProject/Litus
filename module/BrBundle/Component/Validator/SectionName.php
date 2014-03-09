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

namespace BrBundle\Component\Validator;

use CommonBundle\Component\Util\Url,
    Doctrine\ORM\EntityManager,
    BrBundle\Entity\Contract\Section;

/**
 * Matches the given section name against the database to check whether it is
 * unique or not.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class SectionName extends \Zend\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @var \BrBundle\Entity\Contract\Section The section exluded from this check
     */
    private $_section;

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The section name already exists'
    );

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \BrBundle\Entity\Contract\Section The section exluded from this check
     * @param mixed                       $opts          The validator's options
     */
    public function __construct(EntityManager $entityManager, Section $section = null, $opts = null)
    {
        parent::__construct($opts);

        $this->_entityManager = $entityManager;
        $this->_section = $section;
    }

    /**
     * Returns true if no matching record is found in the database.
     *
     * @param  string  $value   The value of the field that will be validated
     * @param  array   $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $section = $this->_entityManager
            ->getRepository('BrBundle\Entity\Contract\Section')
            ->findOneByName($value);

        if (null === $section || ($this->_section && ($section == $this->_section)))
            return true;

        $this->error(self::NOT_VALID);

        return false;
    }
}

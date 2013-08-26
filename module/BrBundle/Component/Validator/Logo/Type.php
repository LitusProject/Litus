<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace BrBundle\Component\Validator\Logo;

use Doctrine\ORM\EntityManager,
    BrBundle\Entity\Company;

/**
 * Matches the given company against the database to check whether the logo type
 * already exists or not.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Type extends \Zend\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @var \BrBundle\Entity\Company The company exluded from this check
     */
    private $_company;

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The logo type already exists for this company'
    );

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \BrBundle\Entity\Company The company exluded from this check
     * @param mixed $opts The validator's options
     */
    public function __construct(EntityManager $entityManager, Company $company = null, $opts = null)
    {
        parent::__construct($opts);

        $this->_entityManager = $entityManager;
        $this->_company = $company;
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

        $logo = $this->_entityManager
            ->getRepository('BrBundle\Entity\Company\Logo')
            ->findOneByTypeAndCompany($value, $this->_company);

        if (null === $logo)
            return true;

        $this->error(self::NOT_VALID);

        return false;
    }
}

<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
 
namespace PageBundle\Component\Validator;

use Doctrine\ORM\EntityManager,
    PageBundle\Entity\Nodes\Translation;

/**
 * Matches the given page title against the database to check whether it is
 * unique or not.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Name extends \Zend\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;
    
    /**
     * @var \PageBundle\Entity\Nodes\Translation The translation exluded from this check
     */
    private $_translation;

    /**
     * @var array The error messages
     */
    protected $_messageTemplates = array(
        self::NOT_VALID => 'The page title already exists'
    );

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \PageBundle\Entity\Nodes\Translation The translation exluded from this check
     * @param mixed $opts The validator's options
     */
    public function __construct(EntityManager $entityManager, Translation $translation = null, $opts = null)
    {
        parent::__construct($opts);

        $this->_entityManager = $entityManager;
        $this->_translation = $translation;
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

        $page = $this->_entityManager
            ->getRepository('PageBundle\Entity\Nodes\Translation')
            ->findOneByName(str_replace(' ', '_', strtolower($value)));
                
        if (null === $page || ($this->_translation && $page == $this->_translation))
            return true;

        $this->error(self::NOT_VALID);
        
        return false;
    }
}

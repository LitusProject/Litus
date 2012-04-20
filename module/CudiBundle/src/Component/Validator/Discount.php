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
 
namespace CudiBundle\Component\Validator;

use CudiBundle\Entity\Article,
    Doctrine\ORM\EntityManager;

class Discount extends \Zend\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';
    
    /**
     * @var \CudiBundle\Entity\Article
     */
    private $_article;
    
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;
    
    
    /**
     * Error messages
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_VALID => 'The discount already exist!'
    );
    
    /**
     * Create a new Discount validator.
     *
     * @param mixed $opts The validator's options
     */
    public function __construct(Article $article, EntityManager $entityManager, $opts = null)
    {
    	parent::__construct($opts);
    	
    	$this->_article = $article;
    	$this->_entityManager = $entityManager;
    }


    /**
     * Returns true if and only if a field name has been set, the field name is available in the
     * context, and the value of that field is valid.
     *
     * @param string $value The value of the field that will be validated
     * @param array $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);
        
        $type = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Articles\Discount\Type')
            ->findOneById($value); 
		
		$discount = $this->_entityManager
			->getRepository('CudiBundle\Entity\Articles\Discount\Discount')
			->findOneByArticleAndType($this->_article, $type);
		
        if (null === $discount)
            return true;

        $this->error(self::NOT_VALID);
        return false;
    }
}
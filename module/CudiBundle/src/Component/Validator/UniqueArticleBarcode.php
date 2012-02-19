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

use Doctrine\ORM\EntityManager;

class UniqueArticleBarcode extends \Zend\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';
   	
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;
	
	/**
	 * @var mixed The ids to be ignored
	 */
   	private $_ignoreIds = array();

    /**
     * Error messages
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_VALID => 'The article barcode already exists'
    );
    
    /**
     * Create a new Unique Article Barcode validator.
     *
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param mixed $ignoreIds The ids to be ignored
     * @param mixed $opts The validator's options
     */
    public function __construct(EntityManager $entityManager, $ignoreIds = array(), $opts = null)
    {
    	parent::__construct($opts);
    	
    	$this->_entityManager = $entityManager;
    }


    /**
     * Returns true if and only if a field name has been set, the field name is available in the
     * context, and the value of that field unique and valid.
     *
     * @param string $value The value of the field that will be validated
     * @param array $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

		$article = $this->_entityManager
			->getRepository('CudiBundle\Entity\Stock\StockItem')
			->findOneByBarcode($value);

       	if (null === $article || in_array($article->getId(), $this->_ignoreIds))
            return true;

        $this->_error(self::NOT_VALID);
        return false;
    }
}
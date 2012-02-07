<?php

namespace CudiBundle\Component\Validator;

use Doctrine\ORM\EntityManager;

class ArticleBarcode extends \Zend\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';
    
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
        self::NOT_VALID => 'The article barcode does not exist'
    );
    
    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param mixed $opts The validator's options
     */
    public function __construct(EntityManager $entityManager, $opts = null)
    {
    	parent::__construct($opts);
    	
    	$this->_entityManager = $entityManager;
    }


    /**
     * Returns true if and only if a field name has been set, the field name is available in the
     * context, and the value of that field name matches the provided value.
     *
     * @param string $value The value of the field that will be validated
     * @param array $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->_setValue($value);

		$article = $this->_entityManager
			->getRepository('Litus\Entity\Cudi\Stock\StockItem')
			->findOneByBarcode($value);
		
        if (null !== $article)
            return true;

        $this->_error(self::NOT_VALID);
        return false;
    }
}
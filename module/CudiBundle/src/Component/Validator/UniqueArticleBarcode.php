<?php

namespace Litus\Validator;

use \Litus\Application\Resource\Doctrine as DoctrineResource;

use \Zend\Registry;

class UniqueArticleBarcode extends \Zend\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';
   	
   	private $_ignoreIds = array();

    /**
     * Error messages
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_VALID => 'The article barcode does already exist'
    );
    
    public function __construct($ignoreIds = array()) {
		$this->_ignoreIds = $ignoreIds;
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

		$article = Registry::get(DoctrineResource::REGISTRY_KEY)
			->getRepository('Litus\Entity\Cudi\Stock\StockItem')
			->findOneByBarcode($value);

       	if (null === $article || in_array($article->getId(), $this->_ignoreIds))
            return true;

        $this->_error(self::NOT_VALID);
        return false;
    }
}
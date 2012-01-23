<?php

namespace Litus\Validator;

use \Litus\Application\Resource\Doctrine as DoctrineResource;

use \Zend\Registry;

class Username extends \Zend\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    /**
     * Error messages
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_VALID => 'The username does not exist'
    );


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

		$person = Registry::get(DoctrineResource::REGISTRY_KEY)
			->getRepository('Litus\Entity\Users\Person')
			->findOneByUsername($value);
		
        if (null !== $person)
            return true;

        $this->_error(self::NOT_VALID);
        return false;
    }
}
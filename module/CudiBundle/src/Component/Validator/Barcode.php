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

class Barcode extends \Zend\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';
    
    /**
     * @var integer
     */
    private $_length;

    /**
     * Error messages
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_VALID => 'The barcode is not valid'
    );
    
    /**
     * Create a new Barcode validator.
     *
     * @param integer $length The barcode length
     * @param mixed $opts The validator's options
     */
    public function __construct($length = 12, $opts = null)
    {
    	parent::__construct($opts);
    	
    	$this->_length = $length;
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
        
        if (! is_numeric($value)) {
        	$this->error(self::NOT_VALID);
        	return false;
        }
		
		if (strlen($value) == $this->_length
		    || strlen($value) == $this->_length + 1)
		    return true;

        $this->error(self::NOT_VALID);
        return false;
    }
}
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
 
namespace CommonBundle\Component\Validator;

/**
 * Verifies whether the given value is in a uri.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Uri extends \Zend\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    /**
     * @var array The error messages
     */
    protected $_messageTemplates = array(
        self::NOT_VALID => 'The uri is not valid'
    );
        
    /**
     * Returns true if the uri has the right format.
     *
     * @param string $value The value of the field that will be validated
     * @param array $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);
        
        $valid = \Zend\Uri\Uri::validateHost($value);
        
        if ($valid)
            return true;

        $this->error(self::NOT_VALID);
        
        return false;
    }
}

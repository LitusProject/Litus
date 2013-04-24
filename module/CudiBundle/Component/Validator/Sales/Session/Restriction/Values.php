<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
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

namespace CudiBundle\Component\Validator\Sales\Session\Restriction;

use Doctrine\ORM\EntityManager;

/**
 * Check the end value is after the start value
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Values extends \Zend\Validator\AbstractValidator
{
    /**
     * @var string The error codes
     */
    const NOT_VALID = 'notSame';

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The value must be greater than %start_value%',
    );

    /**
     * @var array The message variables
     */
    protected $messageVariables = array(
        'start_value'  => '_startValue',
    );

    /**
     * Original start value against which to validate
     * @var string
     */
    protected $_startValue;

    /**
     * Original start value against which to validate
     * @var string
     */
    protected $_startValueName;

    /**
     * Sets validator options
     *
     * @param mixed $token
     * @param string $format
     * @return void
     */
    public function __construct($startValue = null)
    {
        parent::__construct(is_array($startValue) ? $startValue : null);

        $this->_startValueName = $startValue;
    }

    /**
     * Returns true if and only if the end date is after the start date
     *
     * @param  mixed $value
     * @param  array $context
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        if (null === $value || '' == $value)
            return true;

        if (($context !== null) && isset($context) && array_key_exists($this->_startValueName, $context)) {
            $startValue = $context[$this->_startValueName];
            $this->_startValue = $startValue;
        } else {
            $this->error(self::NOT_VALID);
            return false;
        }

        if ($startValue === null) {
            $this->error(self::NOT_VALID);
            return false;
        }

        if ($startValue > $value) {
            $this->error(self::NOT_VALID);
            return false;
        }

        return true;
    }
}

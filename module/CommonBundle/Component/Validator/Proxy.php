<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\Validator;

use \Zend\Validator\AbstractValidator;

/**
 * Conditionaly verify an input
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Proxy extends \Zend\Validator\AbstractValidator
{
    /**
     * @var AbstractValidator
     */
    private $_validator;

    /**
     * @var mixed
     */
    private $_condition;

    public function __construct(AbstractValidator $validator, $condition)
    {
        parent::__construct();

        $this->_validator = $validator;
        $this->_condition = $condition;
    }

    /**
     * Returns array of validation failure messages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->_validator->getMessages();
    }

    public function isValid($value)
    {
        $this->setValue($value);

        if (is_callable($this->_condition)) {
            $result = call_user_func($this->_condition);
        } else {
            $result = (bool) $this->_condition;
        }

        if ($result) {
            $valid = $this->_validator->isValid($value);

            return $valid;
        }

        return true;
    }
}

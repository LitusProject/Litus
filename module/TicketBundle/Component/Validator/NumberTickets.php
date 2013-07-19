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

namespace TicketBundle\Component\Validator;

/**
 * Check whether number of member + number of non member does not exceed max
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class NumberTickets extends \Zend\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    /**
     * @var integer
     */
    private $_max;

    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The number of tickets exceeds the maximum'
    );

    /**
     * Create a new Article Barcode validator.
     *
     * @param integer $max The max number of tickets
     * @param mixed $opts The validator's options
     */
    public function __construct($max, $opts = null)
    {
        parent::__construct($opts);

        $this->_max = $max;
    }


    /**
     * Returns true if these does not exceed max
     *
     * @param string $value The value of the field that will be validated
     * @param array $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        if ($value + $context['number_member'] > $this->_max && $this->_max != 0) {
            $this->error(self::NOT_VALID);
            return false;
        }

        return true;
    }
}

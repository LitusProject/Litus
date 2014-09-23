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

use DateTime;

/**
 * Matches the given faq title against the database to check whether it is
 * unique or not.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class DateCompare extends AbstractValidator
{
    /**
     * @var string The error codes
     */
    const NOT_VALID = 'notSame';

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The date must be after %end_date%',
    );

    /**
     * @var array The message variables
     */
    protected $messageVariables = array(
        'end_date'  => '_endDate',
    );

    /**
     * Original end date against which to validate
     * @var string
     */
    protected $_endDate;

    /**
     * @var string
     */
    private $_format;

    /**
     * Sets validator options
     *
     * @param  string $format
     * @param  string $endDate
     * @return void
     */
    public function __construct($endDate = null, $format)
    {
        parent::__construct(is_array($endDate) ? $endDate : null);

        $this->_endDate = $endDate;
        $this->_format = $format;
    }

    /**
     * Returns true if and only if the end date is after the start date
     *
     * @param  mixed   $value
     * @param  array   $context
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        if (null === $value || '' == $value)
            return true;

        if ('now' == $this->_endDate) {
            $endDate = 'now';
        } elseif (null === $endDate = self::getFormValue($context, $this->_endDate)) {
            $this->error(self::NOT_VALID);

            return false;
        }

        if (DateTime::createFromFormat($this->_format, $value) <= DateTime::createFromFormat($this->_format, $endDate)) {
            $this->error(self::NOT_VALID);

            return false;
        }

        return true;
    }
}

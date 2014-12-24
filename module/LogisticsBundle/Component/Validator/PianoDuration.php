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

namespace LogisticsBundle\Component\Validator;

use DateTime,
    Doctrine\ORM\EntityManager;

/**
 * Checks whether the duration is not to long.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class PianoDuration extends \CommonBundle\Component\Validator\AbstractValidator
{
    /**
     * @const string The error codes
     */
    const TO_LONG        = 'toLong';
    const NO_START_DATE  = 'noStartDate';
    const INVALID_FORMAT = 'invalidFormat';

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NO_START_DATE  => 'There was no start date found',
        self::TO_LONG        => 'The reservation is to long',
        self::INVALID_FORMAT => 'One of the dates is not in the correct format',
    );

    /**
     * @var string The start date of the interval
     */
    private $_startDate;

    /**
     * @var string
     */
    private $_format;

    /**
     * @var EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * Sets validator options
     *
     * @param  string        $format
     * @param  string        $startDate
     * @param  EntityManager $entityManager
     * @return void
     */
    public function __construct($startDate, $format, $entityManager)
    {
        parent::__construct();

        $this->_startDate = $startDate;
        $this->_format = $format;
        $this->_entityManager = $entityManager;
    }

    /**
     * Returns true if and only if no other reservation exists for the resource that conflicts with the new one.
     *
     * @param  mixed   $value
     * @param  array   $context
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        if (null === $startDate = $this->getFormValue($context, $this->_startDate)) {
            $this->error(self::NO_START_DATE);

            return false;
        }

        $startDate = DateTime::createFromFormat($this->_format, $startDate);
        $endDate = DateTime::createFromFormat($this->_format, $value);

        if (!$startDate || !$endDate) {
            $this->error(self::INVALID_FORMAT);

            return false;
        }

        $maxDuration = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('logistics.piano_time_slot_max_duration');

        $diff = $endDate->diff($startDate);

        if ($diff->format('%i') + ($diff->format('%h') * 60) > $maxDuration) {
            $this->error(self::TO_LONG);

            return false;
        }

        return true;
    }
}

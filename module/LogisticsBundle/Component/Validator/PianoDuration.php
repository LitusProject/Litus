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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace LogisticsBundle\Component\Validator;

use DateTime;

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
    const TO_LONG = 'toLong';
    const NO_START_DATE = 'noStartDate';
    const INVALID_FORMAT = 'invalidFormat';

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NO_START_DATE  => 'There was no start date found',
        self::TO_LONG        => 'The reservation is too long',
        self::INVALID_FORMAT => 'One of the dates is not in the correct format',
    );

    protected $options = array(
        'start_date' => '',
        'format'     => false,
    );

    /**
     * Sets validator options
     *
     * @param integer|array|\Traversable $options
     */
    public function __construct($options = array())
    {
        if (!is_array($options)) {
            $args = func_get_args();
            $options = array();
            $options['start_date'] = array_shift($args);
            $options['format'] = array_shift($args);
        }

        parent::__construct($options);
    }

    /**
     * Returns true if and only if no other reservation exists for the resource that conflicts with the new one.
     *
     * @param  string     $value   The value of the field that will be validated
     * @param  array|null $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        if ($startDate === null = $this->getFormValue($context, $this->options['start_date'])) {
            $this->error(self::NO_START_DATE);

            return false;
        }

        $startDate = DateTime::createFromFormat($this->options['format'], $startDate);
        $endDate = DateTime::createFromFormat($this->options['format'], $value);

        if (!$startDate || !$endDate) {
            $this->error(self::INVALID_FORMAT);

            return false;
        }

        $maxDuration = $this->getEntityManager()
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

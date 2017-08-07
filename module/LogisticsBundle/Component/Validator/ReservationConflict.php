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
 * Checks whether no reservation exists yet for the given resource.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class ReservationConflict extends \CommonBundle\Component\Validator\AbstractValidator
{
    /**
     * @const string The error codes
     */
    const CONFLICT_EXISTS = 'conflictExists';
    const INVALID_FORMAT  = 'invalidFormat';
    const NO_START_DATE   = 'noStartDate';

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NO_START_DATE   => 'There was no start date found',
        self::CONFLICT_EXISTS => 'A conflicting reservation already exists for this resource',
        self::INVALID_FORMAT  => 'One of the dates is not in the correct format',
    );

    protected $options = array(
        'start_date' => '',
        'format' => false,
        'resource' => null,
        'reservation_id' => -1,
    );

    /**
     * Sets validator options
     *
     * @param int|array|\Traversable $options
     */
    public function __construct($options = array())
    {
        if (!is_array($options)) {
            $args = func_get_args();
            $options = array();
            $options['start_date'] = array_shift($args);
            $options['format'] = array_shift($args);
            $options['resource'] = array_shift($args);
            $options['reservation_id'] = array_shift($args);
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

        if (null === $startDate = self::getFormValue($context, $this->options['start_date'])) {
            $this->error(self::NO_START_DATE);

            return false;
        }

        $repository = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Reservation\ReservableResource');
        $resource = $repository->findOneByName($this->options['resource']);

        $startDate = DateTime::createFromFormat($this->options['format'], $startDate);
        $endDate = DateTime::createFromFormat($this->options['format'], $value);

        if (!$startDate || !$endDate) {
            $this->error(self::INVALID_FORMAT);

            return false;
        }

        $repository = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Reservation\Reservation');

        $conflicting = $repository->findAllConflictingIgnoringId($startDate, $endDate, $resource, $this->options['reservation_id']);

        if (isset($conflicting[0])) {
            $this->error(self::CONFLICT_EXISTS);

            return false;
        }

        return true;
    }
}

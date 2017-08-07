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

namespace TicketBundle\Component\Validator;

use DateTime;

/**
 * Check the booking close date is not after the event's date
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Date extends \CommonBundle\Component\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    protected $options = array(
        'format' => '',
    );

    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The booking close date cannot be after the event',
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
            $options['format'] = array_shift($args);
        }

        parent::__construct($options);
    }

    /**
     * Returns true if these does not exceed max
     *
     * @param  string     $value   The value of the field that will be validated
     * @param  array|null $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        if (!is_numeric($context['event'])) {
            return false;
        }

        $activity = $this->getEntityManager()
            ->getRepository('CalendarBundle\Entity\Node\Event')
            ->findOneById($context['event']);

        if (null === $activity || $activity->getStartDate() >= DateTime::createFromFormat($this->options['format'], $value)) {
            return true;
        }

        $this->error(self::NOT_VALID);

        return false;
    }
}

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

namespace FormBundle\Component\Validator;

/**
 * Matches the timeslot for occupation of user
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class TimeSlot extends \CommonBundle\Component\Validator\AbstractValidator
{
    /**
     * @var string The error codes
     */
    const OCCUPIED = 'occupied';
    const ALREADY_SELECTED = 'alreadySelected';

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::OCCUPIED         => 'There is already a subscription at this time',
        self::ALREADY_SELECTED => 'You have already a subscription at this time',
    );

    protected $options = array(
        'timeslot' => null,
        'person' => null,
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
            $options['timeslot'] = array_shift($args);
            $options['person'] = array_shift($args);
        }

        parent::__construct($options);
    }

    /**
     * Returns true if and only if the end date is after the start date
     *
     * @param  string     $value   The value of the field that will be validated
     * @param  array|null $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $valid = true;

        if (isset($value) && $value && null !== $this->options['person']) {
            $occupation = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Field\TimeSlot')
                ->findOneOccupationByPersonAndTime($this->options['person'], $this->options['timeslot']->getStartDate(), $this->options['timeslot']->getEndDate());

            // No overlap with selections of other people
            if (null !== $occupation && $occupation->getFormEntry()->getCreationPerson()->getId() != $this->options['person']->getId()) {
                $this->error(self::OCCUPIED);
                $valid = false;
            }

            $conflictingSlots = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Field\TimeSlot')
                ->findAllConflictingByFormAndTime($this->options['timeslot']->getForm(), $this->options['timeslot']->getStartDate(), $this->options['timeslot']->getEndDate());

            // No overlap with other selections in this form
            foreach ($conflictingSlots as $conflictingSlot) {
                if ($conflictingSlot->getId() == $this->options['timeslot']->getId()) {
                    continue;
                }

                if (isset($context['field-' . $conflictingSlot->getId()]) && $context['field-' . $conflictingSlot->getId()]) {
                    $this->error(self::ALREADY_SELECTED);
                    $valid = false;
                }
            }
        }

        return $valid;
    }
}

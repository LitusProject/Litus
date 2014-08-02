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

namespace FormBundle\Component\Validator;

use CommonBundle\Entity\User\Person as PersonEntity,
    Doctrine\ORM\EntityManager,
    FormBundle\Entity\Field\TimeSlot as TimeSlotField;

/**
 * Matches the timeslot for occupation of user
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class TimeSlot extends \Zend\Validator\AbstractValidator
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

    /**
     * @var TimeSlotField
     */
    private $_timeSlot;

    /**
     * @var EntityManager
     */
    private $_entityManager;

    /**
     * @var \CommonBundle\Entity\User\Person
     */
    private $_person;

    /**
     * Sets validator options
     *
     * @param  TimeSlotField                    $timeSlot
     * @param  EntityManager                    $entityManager
     * @param  \CommonBundle\Entity\User\Person $person
     * @return void
     */
    public function __construct(TimeSlotField $timeSlot, EntityManager $entityManager, PersonEntity $person = null, $opts = null)
    {
        parent::__construct($opts);

        $this->_timeSlot = $timeSlot;
        $this->_entityManager = $entityManager;
        $this->_person = $person;
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

        $valid = true;

        if (isset($value) && $value && null !== $this->_person) {
            $occupation = $this->_entityManager
                ->getRepository('FormBundle\Entity\Field\TimeSlot')
                ->findOneOccupationByPersonAndTime($this->_person, $this->_timeSlot->getStartDate(), $this->_timeSlot->getEndDate());

            // No overlap with selections of other people
            if (null !== $occupation && $occupation->getFormEntry()->getCreationPerson()->getId() != $this->_person->getId()) {
                $this->error(self::OCCUPIED);
                $valid = false;
            }

            $conflictingSlots = $this->_entityManager
                ->getRepository('FormBundle\Entity\Field\TimeSlot')
                ->findAllConflictingByFormAndTime($this->_timeSlot->getForm(), $this->_timeSlot->getStartDate(), $this->_timeSlot->getEndDate());

            // No overlap with other selections in this form
            foreach ($conflictingSlots as $conflictingSlot) {
                if ($conflictingSlot->getId() == $this->_timeSlot->getId())
                    continue;

                if (isset($context['field-' . $conflictingSlot->getId()]) && $context['field-' . $conflictingSlot->getId()]) {
                    $this->error(self::ALREADY_SELECTED);
                    $valid = false;
                }
            }
        }

        return $valid;
    }
}

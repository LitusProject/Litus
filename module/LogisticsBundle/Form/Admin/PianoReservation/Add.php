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

namespace LogisticsBundle\Form\Admin\PianoReservation;

use CommonBundle\Component\Form\Admin\Element\Checkbox,
    CommonBundle\Component\Form\Admin\Element\Hidden,
    CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Form\Admin\Element\Textarea,
    CommonBundle\Component\Validator\DateCompare as DateCompareValidator,
    DateInterval,
    DateTime,
    Doctrine\ORM\EntityManager,
    CommonBundle\Component\Validator\Academic as AcademicValidator,
    LogisticsBundle\Component\Validator\PianoReservationConflict as ReservationConflictValidator,
    LogisticsBundle\Component\Validator\PianoDuration as PianoDurationValidator,
    LogisticsBundle\Entity\Reservation\PianoReservation,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * The form used to add a new Reservation.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int             $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;

        $field = new Hidden('player_id');
        $field->setAttribute('id', 'playerId');
        $this->add($field);

        $field = new Text('player');
        $field->setLabel('Player')
            ->setRequired()
            ->setAttribute('id', 'playerSearch')
            ->setAttribute('autocomplete', 'off')
            ->setAttribute('data-provide', 'typeahead');
        $this->add($field);

        $field = new Select('start_date');
        $field->setLabel('Start Date')
            ->setAttribute('options', $this->_getTimeSlots(true))
            ->setRequired();
        $this->add($field);

        $field = new Select('end_date');
        $field->setLabel('End Date')
            ->setAttribute('options', $this->_getTimeSlots(false))
            ->setRequired();
        $this->add($field);

        $field = new Textarea('additional_info');
        $field->setLabel('Additional Information');
        $this->add($field);

        $field = new Checkbox('confirmed');
        $field->setLabel('Confirmed');
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'reservation_add');
        $this->add($field);
    }

    /**
     * @param boolean $isStart
     */
    private function _getTimeSlots($isStart)
    {
        $config = unserialize(
            $this->_entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('logistics.piano_time_slots')
        );

        $slotDuration = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('logistics.piano_time_slot_duration');

        $now = new DateTime();
        $maxDate = new DateTime();
        $maxDate->add(
            new DateInterval(
                $this->_entityManager
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('logistics.piano_reservation_max_in_advance')
            )
        );

        $list = array();

        while ($now < $maxDate) {
            if (null !== $config[$now->format('N')]) {
                foreach ($config[$now->format('N')] as $slot) {
                    $startSlot = clone $now;
                    $startSlot->setTime(
                        substr($slot['start'], 0, strpos($slot['start'], ':')),
                        substr($slot['start'], strpos($slot['start'], ':') + 1)
                    );
                    $firstSlot = clone $startSlot;

                    $lastSlot = clone $now;
                    $lastSlot->setTime(
                        substr($slot['end'], 0, strpos($slot['end'], ':')),
                        substr($slot['end'], strpos($slot['end'], ':') + 1)
                    );

                    while ($startSlot <= $lastSlot) {
                        if (($isStart && $startSlot == $lastSlot) || (!$isStart && $startSlot == $firstSlot)) {
                            $startSlot->add(new DateInterval('PT' . $slotDuration . 'M'));
                            continue;
                        }

                        $occupied = $this->_entityManager
                            ->getRepository('LogisticsBundle\Entity\Reservation\PianoReservation')
                            ->isTimeInExistingReservation($startSlot, $isStart);

                        if (!$occupied)
                            $list[$startSlot->format('D d/m/Y H:i')] = $startSlot->format('D d/m/Y H:i');

                        $startSlot->add(new DateInterval('PT' . $slotDuration . 'M'));
                    }
                }
            }

            $now->add(new DateInterval('P1D'));
        }

        return $list;
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'start_date',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'date',
                            'options' => array(
                                'format' => 'D d/m/Y H:i',
                            ),
                        ),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'end_date',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'date',
                            'options' => array(
                                'format' => 'D d/m/Y H:i',
                            ),
                        ),
                        new DateCompareValidator('start_date', 'D d/m/Y H:i'),
                        new ReservationConflictValidator('start_date', 'D d/m/Y H:i', PianoReservation::PIANO_RESOURCE_NAME, $this->_entityManager),
                        new PianoDurationValidator('start_date', 'D d/m/Y H:i', $this->_entityManager),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'additional_info',
                    'required' => false,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'player_id',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new AcademicValidator(
                            $this->_entityManager,
                            array(
                                'byId' => true,
                            )
                        )
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'player',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        return $inputFilter;

    }
}

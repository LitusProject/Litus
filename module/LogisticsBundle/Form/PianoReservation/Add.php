<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace LogisticsBundle\Form\PianoReservation;

use CommonBundle\Component\Form\Bootstrap\Element\Select,
    CommonBundle\Component\Form\Bootstrap\Element\Submit,
    CommonBundle\Component\Validator\DateCompare as DateCompareValidator,
    DateInterval,
    DateTime,
    Doctrine\ORM\EntityManager,
    LogisticsBundle\Component\Validator\PianoReservationConflict as ReservationConflictValidator,
    LogisticsBundle\Component\Validator\PianoDuration as PianoDurationValidator,
    LogisticsBundle\Entity\Reservation\PianoReservation,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * The form used to add a new Reservation.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @var array
     */
    private $_weeks;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;

        $this->_weeks = $this->_getTimeSlots();

        foreach($this->_weeks as $key => $week) {
            $field = new Select('start_date_' . $key);
            $field->setLabel('Start Date')
                ->setAttribute('options', $week['slotsStart']);
            $this->add($field);

            $field = new Select('end_date_' . $key);
            $field->setLabel('End Date')
                ->setAttribute('options', $week['slotsEnd']);
            $this->add($field);

            $field = new Submit('submit_' . $key);
            $field->setValue('Book');
            $this->add($field);
        }
    }

    public function getWeeks()
    {
        return $this->_weeks;
    }

    private function _getTimeSlots()
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

        $weeks = array();

        while($now < $maxDate) {
            $listStart = array();
            $listEnd = array();
            if (null !== $config[$now->format('N')]) {
                foreach($config[$now->format('N')] as $slot) {
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

                    while($startSlot <= $lastSlot) {
                        if ($startSlot != $lastSlot) {
                            $occupied = $this->_entityManager
                                ->getRepository('LogisticsBundle\Entity\Reservation\PianoReservation')
                                ->isTimeInExistingReservation($startSlot, true);

                            $listStart[] = array(
                                'label' => $startSlot->format('D d/m/Y H:i'),
                                'value' => $startSlot->format('D d/m/Y H:i'),
                                'attributes' => array(
                                    'disabled' => $occupied,
                                )
                            );
                        }

                        if ($startSlot != $firstSlot) {
                            $occupied = $this->_entityManager
                                ->getRepository('LogisticsBundle\Entity\Reservation\PianoReservation')
                                ->isTimeInExistingReservation($startSlot, false);

                            $listEnd[] = array(
                                'label' => $startSlot->format('D d/m/Y H:i'),
                                'value' => $startSlot->format('D d/m/Y H:i'),
                                'attributes' => array(
                                    'disabled' => $occupied,
                                )
                            );
                        }

                        $startSlot->add(new DateInterval('PT' . $slotDuration . 'M'));
                    }
                }
            }

            if (sizeof($listStart) > 0 && sizeof($listEnd) > 0) {
                if (!isset($weeks[$now->format('W')])) {
                    $end = (clone $now);
                    $end->add(new DateInterval('P6D'));
                    $weeks[$now->format('W')] = array(
                        'start' => clone $now,
                        'end' => $end,
                        'slotsStart' => array(),
                        'slotsEnd' => array(),
                    );
                }

                $weeks[$now->format('W')]['slotsStart'] = array_merge($weeks[$now->format('W')]['slotsStart'], $listStart);
                $weeks[$now->format('W')]['slotsEnd'] = array_merge($weeks[$now->format('W')]['slotsEnd'], $listEnd);
            }

            $now->add(new DateInterval('P1D'));
        }

        return $weeks;
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        foreach($this->_weeks as $key => $week) {
            if (!isset($this->data['submit_' . $key]))
                continue;

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'start_date_' . $key,
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
                        'name'     => 'end_date_' . $key,
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
                            new DateCompareValidator('start_date_' . $key, 'D d/m/Y H:i'),
                            new ReservationConflictValidator('start_date_' . $key, 'D d/m/Y H:i', PianoReservation::PIANO_RESOURCE_NAME, $this->_entityManager),
                            new PianoDurationValidator('start_date_' . $key, 'D d/m/Y H:i', $this->_entityManager),
                        ),
                    )
                )
            );
        }

        return $inputFilter;
    }
}
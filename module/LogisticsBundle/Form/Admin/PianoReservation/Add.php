<?php

namespace LogisticsBundle\Form\Admin\PianoReservation;

use DateInterval;
use DateTime;
use LogisticsBundle\Entity\Reservation\Piano as PianoReservation;

/**
 * The form used to add a new Reservation.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'LogisticsBundle\Hydrator\Reservation\Piano';

    /**
     * @var PianoReservation|null
     */
    protected $reservation;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'typeahead',
                'name'     => 'player',
                'label'    => 'Player',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'validators' => array(
                            array('name' => 'TypeaheadPerson'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'start_date',
                'label'      => 'Start Date',
                'required'   => true,
                'attributes' => array(
                    'options' => $this->getTimeSlots(true),
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name'    => 'Date',
                                'options' => array(
                                    'format' => 'd/m/Y H:i',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'end_date',
                'label'      => 'End Date',
                'required'   => true,
                'attributes' => array(
                    'options' => $this->getTimeSlots(false),
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name'    => 'Date',
                                'options' => array(
                                    'format' => 'd/m/Y H:i',
                                ),
                            ),
                            array(
                                'name'    => 'DateCompare',
                                'options' => array(
                                    'first_date' => 'start_date',
                                    'format'     => 'd/m/Y H:i',
                                ),
                            ),
                            array(
                                'name'    => 'PianoReservationConflict',
                                'options' => array(
                                    'start_date'     => 'start_date',
                                    'format'         => 'd/m/Y H:i',
                                    'resource'       => PianoReservation::RESOURCE_NAME,
                                    'reservation_id' => $this->reservation !== null ? $this->reservation->getId() : null,
                                ),
                            ),
                            array(
                                'name'    => 'PianoDuration',
                                'options' => array(
                                    'start_date' => 'start_date',
                                    'format'     => 'd/m/Y H:i',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'    => 'textarea',
                'name'    => 'additional_info',
                'label'   => 'Additional Info',
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'confirmed',
                'label' => 'Confirmed',
            )
        );

        $this->addSubmit('Add', 'reservation_add');

        if ($this->reservation !== null) {
            $this->bind($this->reservation);
        }
    }

    /**
     * @param  PianoReservation $reservation
     * @return self
     */
    public function setReservation(PianoReservation $reservation)
    {
        $this->reservation = $reservation;

        return $this;
    }

    /**
     * @param boolean $isStart
     */
    private function getTimeSlots($isStart)
    {
        $config = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('logistics.piano_time_slots')
        );

        $slotDuration = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('logistics.piano_time_slot_duration');

        $now = new DateTime();
        $maxDate = new DateTime();
        $maxDate->add(
            new DateInterval(
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('logistics.piano_reservation_max_in_advance')
            )
        );

        $list = array();

        while ($now < $maxDate) {
            if ($config[$now->format('N')] !== null) {
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

                        $occupied = $this->getEntityManager()
                            ->getRepository('LogisticsBundle\Entity\Reservation\Piano')
                            ->isTimeInExistingReservation($startSlot, $isStart);

                        if (!$occupied) {
                            $list[$startSlot->format('d/m/Y H:i')] = $startSlot->format('D d/m/Y H:i');
                        }

                        $startSlot->add(new DateInterval('PT' . $slotDuration . 'M'));
                    }
                }
            }

            $now->add(new DateInterval('P1D'));
        }

        return $list;
    }
}

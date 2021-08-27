<?php

namespace LogisticsBundle\Form\PianoReservation;

use DateInterval;
use DateTime;
use IntlDateFormatter;
use LogisticsBundle\Entity\Reservation\Piano as PianoReservation;

/**
 * The form used to add a new Reservation.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{
    /**
     * @var array List of all possible slots
     */
    private $weeks;

    public function init()
    {
        parent::init();

        $this->weeks = $this->getTimeSlots();

        foreach ($this->weeks as $key => $week) {
            $this->add(
                array(
                    'type'     => 'fieldset',
                    'name'     => 'week_' . $key,
                    'elements' => array(
                        array(
                            'type'       => 'select',
                            'name'       => 'start_date',
                            'label'      => 'Start Date',
                            'attributes' => array(
                                'options' => $week['slotsStart'],
                            ),
                            'options' => array(
                                'input' => array(
                                    'required' => true,
                                    'filters'  => array(
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
                        ),
                        array(
                            'type'       => 'select',
                            'name'       => 'end_date',
                            'label'      => 'End Date',
                            'attributes' => array(
                                'options' => $week['slotsEnd'],
                            ),
                            'options' => array(
                                'input' => array(
                                    'required' => true,
                                    'filters'  => array(
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
                                                'start_date' => 'start_date',
                                                'format'     => 'd/m/Y H:i',
                                                'resource'   => PianoReservation::RESOURCE_NAME,
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
                        ),
                        array(
                            'type'  => 'submit',
                            'name'  => 'submit',
                            'value' => 'Book',
                        ),
                    ),
                )
            );
        }
    }

    private function getTimeSlots()
    {
        $formatter = new IntlDateFormatter(
            $this->getLanguage()->getAbbrev(),
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            date_default_timezone_get(),
            IntlDateFormatter::GREGORIAN,
            'E d/M/Y H:mm'
        );

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

        $weeks = array();
        while ($now < $maxDate) {
            $listStart = array();
            $listEnd = array();
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
                        if ($startSlot != $lastSlot) {
                            $occupied = $this->getEntityManager()
                                ->getRepository('LogisticsBundle\Entity\Reservation\Piano')
                                ->isTimeInExistingReservation($startSlot, true);

                            $listStart[] = array(
                                'label'      => $formatter->format($startSlot),
                                'value'      => $startSlot->format('d/m/Y H:i'),
                                'attributes' => array(
                                    'disabled' => $occupied,
                                ),
                            );
                        }

                        if ($startSlot != $firstSlot) {
                            $occupied = $this->getEntityManager()
                                ->getRepository('LogisticsBundle\Entity\Reservation\Piano')
                                ->isTimeInExistingReservation($startSlot, false);

                            $listEnd[] = array(
                                'label'      => $formatter->format($startSlot),
                                'value'      => $startSlot->format('d/m/Y H:i'),
                                'attributes' => array(
                                    'disabled' => $occupied,
                                ),
                            );
                        }

                        $startSlot->add(new DateInterval('PT' . $slotDuration . 'M'));
                    }
                }
            }

            if (count($listStart) > 0 && count($listEnd) > 0) {
                if (!isset($weeks[$now->format('W')])) {
                    $end = (clone $now);
                    $end->add(new DateInterval('P6D'));
                    $weeks[$now->format('W')] = array(
                        'start'      => clone $now,
                        'end'        => $end,
                        'slotsStart' => array(),
                        'slotsEnd'   => array(),
                    );
                }

                $weeks[$now->format('W')]['slotsStart'] = array_merge($weeks[$now->format('W')]['slotsStart'], $listStart);
                $weeks[$now->format('W')]['slotsEnd'] = array_merge($weeks[$now->format('W')]['slotsEnd'], $listEnd);
            }

            $now->add(new DateInterval('P1D'));
        }

        return $weeks;
    }

    public function getWeeks()
    {
        return $this->weeks;
    }

    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();
        foreach ($this->getFieldsets() as $fieldset) {
            if (!isset($this->data[$fieldset->getName()]['submit'])) {
                unset($specs[$fieldset->getName()]);
            }
        }

        return $specs;
    }
}

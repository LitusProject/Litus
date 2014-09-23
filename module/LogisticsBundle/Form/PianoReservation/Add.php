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

namespace LogisticsBundle\Form\PianoReservation;

use CommonBundle\Component\Validator\DateCompare as DateCompareValidator,
    CommonBundle\Entity\General\Language,
    DateInterval,
    DateTime,
    IntlDateFormatter,
    LogisticsBundle\Component\Validator\PianoReservationConflict as ReservationConflictValidator,
    LogisticsBundle\Component\Validator\PianoDuration as PianoDurationValidator,
    LogisticsBundle\Entity\Reservation\PianoReservation;

/**
 * The form used to add a new Reservation.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{
    public function init()
    {
        parent::init();

        $weeks = $this->getTimeSlots();

        foreach ($weeks as $key => $week) {
            $this->add(array(
                'type'       => 'fieldset',
                'name'       => 'week_' . $key,
                'attributes' => array(
                    array(
                        'type'       => 'select',
                        'name'       => 'start_date',
                        'label'      => 'Start Date',
                        'attributes' => array(
                            'options' => $week['slotsStart'],
                        ),
                        'options'    => array(
                            'input' => array(
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
                        'options'    => array(
                            'input' => array(
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
                                    new DateCompareValidator(
                                        array('week_' . $key, 'start_date'),
                                        'D d/m/Y H:i'
                                    ),
                                    new ReservationConflictValidator(
                                        array('week_' . $key, 'start_date'),
                                        'D d/m/Y H:i',
                                        PianoReservation::PIANO_RESOURCE_NAME,
                                        $this->getEntityManager()
                                    ),
                                    new PianoDurationValidator(
                                        array('week_' . $key, 'start_date'),
                                        'D d/m/Y H:i',
                                        $this->getEntityManager()
                                    ),
                                ),
                            ),
                        ),
                    ),
                    array(
                        'type'  => 'submit',
                        'name'  => 'submit',
                        'label' => 'Book',
                    ),
                ),
            ));
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
                        if ($startSlot != $lastSlot) {
                            $occupied = $this->getEntityManager()
                                ->getRepository('LogisticsBundle\Entity\Reservation\PianoReservation')
                                ->isTimeInExistingReservation($startSlot, true);

                            $listStart[] = array(
                                'label' => $formatter->format($startSlot),
                                'value' => $startSlot->format('D d/m/Y H:i'),
                                'attributes' => array(
                                    'disabled' => $occupied,
                                )
                            );
                        }

                        if ($startSlot != $firstSlot) {
                            $occupied = $this->getEntityManager()
                                ->getRepository('LogisticsBundle\Entity\Reservation\PianoReservation')
                                ->isTimeInExistingReservation($startSlot, false);

                            $listEnd[] = array(
                                'label' => $formatter->format($startSlot),
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

    public function getInputFilterSpecification()
    {
        foreach ($this->getFieldsets() as $fieldset) {
            if (!isset($this->data[$fieldset->getName()]['submit']))
                continue;

            return array(
                $fieldset->getName() => $fieldset->getInputSpecification();
            );
        }

        return array();
    }
}

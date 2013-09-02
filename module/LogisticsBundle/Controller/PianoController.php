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

namespace LogisticsBundle\Controller;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    DateInterval,
    DateTime,
    Zend\View\Model\ViewModel;

/**
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class PianoController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function indexAction()
    {
        $slots = $this->_getTimeSlots(true);

        return new ViewModel(
            array(
                'timeSlots' => $slots,
            )
        );
    }

    private function _getTimeSlots($isStart)
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

        $weeks = array();

        while($now < $maxDate) {
            $list = array();
            if (null !== $config[$now->format('N')]) {
                foreach($config[$now->format('N')] as $slot) {
                    $startSlot = clone $now;
                    $startSlot->setTime(
                        substr($slot['start'], 0, strpos($slot['start'], ':')),
                        substr($slot['start'], strpos($slot['start'], ':') + 1)
                    );

                    $endSlot = clone $now;
                    $endSlot->setTime(
                        substr($slot['end'], 0, strpos($slot['end'], ':')),
                        substr($slot['end'], strpos($slot['end'], ':') + 1)
                    );

                    while($startSlot <= $endSlot) {
                        $list[] = array(
                            'date' => clone $startSlot,
                            'occupied' => $this->getEntityManager()
                                ->getRepository('LogisticsBundle\Entity\Reservation\PianoReservation')
                                ->isTimeInExistingReservation($startSlot, $isStart),
                        );

                        $startSlot->add(new DateInterval('PT' . $slotDuration . 'M'));
                    }
                }
            }

            if (!isset($weeks[$now->format('W')])) {
                $end = (clone $now);
                $end->add(new DateInterval('P6D'));
                $weeks[$now->format('W')] = array(
                    'start' => clone $now,
                    'end' => $end,
                    'slots' => array(),
                );
            }

            $weeks[$now->format('W')]['slots'] = array_merge($weeks[$now->format('W')]['slots'], $list);

            $now->add(new DateInterval('P1D'));
        }

        return $weeks;
    }
}
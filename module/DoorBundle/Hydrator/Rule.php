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

namespace DoorBundle\Hydrator;

use DoorBundle\Document\Rule as RuleDocument;

class Rule extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected $entity = 'DoorBundle\Document\Rule';

    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            $repository = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic');

            $academic = ('' == $data['academic']['id'])
                ? $repository->findOneByUsername($data['academic'])
                : $repository->findOneById($data['academic']['id']);

            $object = new RuleDocument(
                $academic
            );
        }

        if (isset($data['start_time']) && null !== $data['start_time']) {
            $object->setStartTime(self::loadTime($data['start_time'])->format('Hi'));
        }

        if (isset($data['end_time']) && null !== $data['end_time']) {
            $object->setEndTime(self::loadTime($data['end_time'])->format('Hi'));
        }

        return $object->setStartDate(self::loadDateTime($data['start_date'] . ' 00:00'))
            ->setEndDate(self::loadDateTime($data['end_date'] . ' 00:00'));
    }

    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array();
        }

        if ($object->getStartTime() === $object->getEndTime()) {
            $startTime = $endTime = null;
        } else {
            $startTime = self::printTime($object->getStartTime());
            $endTime = self::printTime($object->getEndTime());
        }

        return array(
            'start_date' => $object->getStartDate()->format('d/m/Y'),
            'end_date' => $object->getEndDate()->format('d/m/Y'),
            'start_time' => $startTime,
            'end_time' => $endTime,
        );
    }

    /**
     * Prints an integer time as hh:mm
     *
     * @param  int|null $time
     * @return string
     */
    private static function printTime($time)
    {
        $hour = floor($time / 100);
        $mins = $time % 100;

        if ($mins < 10) {
            $mins = '0' . $mins;
        }

        if ($hour < 10) {
            // jQuery timepicker needs hh:mm
            $hour = '0' . $hour;
        }

        return $hour . ':' . $mins;
    }
}

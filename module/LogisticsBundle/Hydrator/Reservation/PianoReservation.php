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

namespace LogisticsBundle\Hydrator\Reservation;

use LogisticsBundle\Entity\Reservation\PianoReservation as PianoReservationEntity;

class PianoReservation extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $std_keys = array('confirmed', 'additional_info');

    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array();
        }

        $data = $this->stdExtract($object, self::$std_keys);

        $player = $object->getPlayer();
        $data['player_id'] = $player->getId();
        $data['player'] = $player->getFullName() . ($player instanceof Academic ? ' - ' . $player->getUniversityIdentification() : '');

        $data['start_date'] = $object->getStartDate()->format('d/m/Y');
        $data['end_date'] = $object->getEndDate()->format('d/m/Y');

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            $resource = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Reservation\ReservableResource')
                ->findOneByName(PianoReservationEntity::PIANO_RESOURCE_NAME);

            $object = new PianoReservationEntity(
                $resource,
                $this->getAuthentication()->getPersonObject()
            );
        }

        if (isset($data['player']) || isset($data['player_id'])) {
            $repository = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic');

            if ('' != $data['player_id']) {
                $player = $repository->findOneById($data['player_id']);
            } else {
                $player = $repository->findOneByUsername($data['player']);
            }

            $object->setPlayer($player);
        }

        if (isset($data['start_date'])) {
            $object->setStartDate(self::loadDate($data['start_date']));
        }

        if (isset($data['end_date'])) {
            $object->setEndDate(self::loadDate($data['end_date']));
        }

        return $this->stdHydrate($data, $object, self::$std_keys);
    }
}

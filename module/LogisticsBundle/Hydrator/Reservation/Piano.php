<?php

namespace LogisticsBundle\Hydrator\Reservation;

use CommonBundle\Entity\User\Person\Academic;
use LogisticsBundle\Entity\Reservation\Piano as PianoReservationEntity;

class Piano extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('confirmed', 'additional_info');

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);

        $player = $object->getPlayer();
        $data['player']['id'] = $player->getId();
        $data['player']['value'] = $player->getFullName() . ($player instanceof Academic ? ' - ' . $player->getUniversityIdentification() : '');

        $data['start_date'] = $object->getStartDate()->format('d/m/Y');
        $data['end_date'] = $object->getEndDate()->format('d/m/Y');

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $resource = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Reservation\Resource')
                ->findOneByName(PianoReservationEntity::RESOURCE_NAME);

            $object = new PianoReservationEntity(
                $resource,
                $this->getPersonEntity()
            );
        }

        $player = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person\Academic')
            ->findOneById($data['player']['id']);

        $object->setPlayer($player);

        if (isset($data['start_date'])) {
            $object->setStartDate(self::loadDateTime($data['start_date']));
        }

        if (isset($data['end_date'])) {
            $object->setEndDate(self::loadDateTime($data['end_date']));
        }

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }
}

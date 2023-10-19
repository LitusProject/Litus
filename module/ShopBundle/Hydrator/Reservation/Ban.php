<?php

namespace ShopBundle\Hydrator\Reservation;

use ShopBundle\Entity\Reservation\Ban as BanEntity;

class Ban extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected function doExtract($object = null)
    {
        error_log("extract");
        return array();
//        error_log("extract");
//        if ($object === null) {
//            return array();
//        }
//
//        $data = $this->stdExtract($object);
//        $data['start_timestamp'] = $object->getStartTimestamp()->format('d/m/Y H:i');
//        $data['end_timestamp'] = $object->getEndTimestamp() ? $object->getEndTimestamp()->format('d/m/Y H:i') : ' ';
////        $data['person']['id'] = $object->getPerson()->getId();
//
//        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        error_log("hydrate");

        if ($object === null) {
            $object = new BanEntity();
        }

        return $object;
//
//
//
//        $object->setStartTimestamp(self::loadDateTime($data['start_timestamp']));
//        $object->setEndTimestamp(self::loadDateTime($data['end_timestamp']));
//
//        $person = $this->getEntityManager()
//            ->getRepository('CommonBundle\Entity\User\Person')
//            ->find($data['person']['id']);
//        $object->setPerson($person);
//
//        return $object;
    }
}

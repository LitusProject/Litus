<?php

namespace ShopBundle\Hydrator\Reservation;

use ShopBundle\Entity\Reservation\Permission as ReservationPermissionEntity;

class Permission extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('reservations_allowed');

    protected function doExtract($object = null)
    {
        return array();
    }

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new ReservationPermissionEntity();
        }

        $object = $this->stdHydrate($data, $object, self::$stdKeys);

        $person = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person')
            ->find($data['person']['id']);
        $object->setPerson($person);

        return $object;
    }
}

<?php

namespace ShopBundle\Hydrator;

use DateTime;
use ShopBundle\Entity\Reservation as ReservationEntity;

class Reservation extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array();

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        return $this->stdExtract($object, self::$stdKeys);
    }

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new ReservationEntity();
        }

        $object->setTimestamp(new DateTime());

        return $object;
    }
}

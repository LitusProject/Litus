<?php

namespace PromBundle\Hydrator;

use PromBundle\Entity\Bus as BusEntity;

class Bus extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array(
        'name',
        'total_seats',
        'direction',
    );

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);

        $data['departure_time'] = $object->getDepartureTime()->format('d/m/Y H:i');

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new BusEntity($this->getCurrentAcademicYear());
        }

        $object->setDepartureTime(self::loadDateTime($data['departure_time']));

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }
}

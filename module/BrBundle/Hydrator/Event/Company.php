<?php

namespace BrBundle\Hydrator\Event;

use BrBundle\Entity\Event as EventEntity;

class Company extends \CommonBundle\Component\Hydrator\Hydrator
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
            $object = new EventEntity($this->getPersonEntity());
        }

        $object = $this->stdHydrate($data, $object, self::$stdKeys);

        return $object;
    }
}

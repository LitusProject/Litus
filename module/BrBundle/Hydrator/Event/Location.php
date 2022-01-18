<?php
namespace BrBundle\Hydrator\Event;

use BrBundle\Entity\Event as EventEntity;
use BrBundle\Entity\Event\Location as LocationEntity;

class Location extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('number', 'x', 'y', 'orientation', 'type');

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }
        $data = $this->stdExtract($object, self::$stdKeys);
        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new LocationEntity();
        }

        $object = $this->stdHydrate($data, $object, self::$stdKeys);
        
        return $object;
    }
}
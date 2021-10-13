<?php

namespace LogisticsBundle\Hydrator\Lease;

use LogisticsBundle\Entity\Lease\Item as ItemEntity;

class Item extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('name', 'barcode', 'additional_info');

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
            $object = new ItemEntity();
        }

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }
}

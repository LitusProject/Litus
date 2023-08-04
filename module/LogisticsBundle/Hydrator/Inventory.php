<?php

namespace LogisticsBundle\Hydrator;

use LogisticsBundle\Entity\Inventory as InventoryEntity;

class Inventory extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('barcode', 'name', 'amount', 'expiry_date', 'category', 'brand', 'perUnit', 'unit');

    protected function doHydrate(array $array, $object = null)
    {
        if ($object === null) {
            $object = new InventoryEntity();
        }

        return $this->stdHydrate($array, $object, self::$stdKeys);
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $array = $this->stdExtract($object, self::$stdKeys);
        $array['perUnit'] = $object->getPerUnit() != ''? $object->getPerUnit(): Null;

        return $array;
    }
}
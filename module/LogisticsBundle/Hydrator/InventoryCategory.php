<?php

namespace LogisticsBundle\Hydrator;

use LogisticsBundle\Entity\InventoryCategory as InventoryCategoryEntity;

/**
 * This hydrator hydrates/extracts Category data.
 *
 * @author Pedro Devogelaere <pedro.devogelaere@vtk.be>
 */
class InventoryCategory extends \CommonBundle\Component\Hydrator\Hydrator
{
    /**
     * @static @var string[] Key attributes to hydrate using the standard method.
     */
    private static array $stdKeys = array('name', 'description');

    protected function doHydrate(array $data, $object = null): object
    {
        if ($object === null) {
            $object = new InventoryCategoryEntity();
        }

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }

    protected function doExtract($object = null): array
    {
        if ($object === null) {
            return array();
        }

        return $this->stdExtract($object, self::$stdKeys);
    }
}

<?php

namespace BrBundle\Hydrator\Event;

use BrBundle\Entity\Event\CompanyMetadata as CompanyMetadataEntity;

class CompanyMetadata extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('master_interests');

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
            $object = new CompanyMetadataEntity();
        }

        $object = $this->stdHydrate($data, $object, self::$stdKeys);

        return $object;
    }
}

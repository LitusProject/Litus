<?php

namespace SecretaryBundle\Hydrator;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;
use SecretaryBundle\Entity\Pull as PullEntity;

class Pull extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('study_nl', 'study_en', 'amount_available', 'available', );

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new PullEntity();
        }

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);

        return $data;
    }
}
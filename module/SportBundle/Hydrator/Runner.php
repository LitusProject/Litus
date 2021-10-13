<?php

namespace SportBundle\Hydrator;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;

class Runner extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('runner_identification');

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            throw new InvalidObjectException();
        }

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        return $this->stdExtract($object, self::$stdKeys);
    }
}

<?php

namespace SyllabusBundle\Hydrator\Study;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;

class SubjectMap extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('mandatory');

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

<?php

namespace SyllabusBundle\Hydrator\Subject;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;

class Reply extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('text');

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            throw new InvalidObjectException();
        }

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }

    protected function doExtract($object = null)
    {
        return array();
    }
}

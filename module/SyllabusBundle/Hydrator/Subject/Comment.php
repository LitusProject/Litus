<?php

namespace SyllabusBundle\Hydrator\Subject;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;

class Comment extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('text', 'type');

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

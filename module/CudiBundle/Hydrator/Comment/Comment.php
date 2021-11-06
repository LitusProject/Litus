<?php

namespace CudiBundle\Hydrator\Comment;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;

class Comment extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('text', 'type');

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
            throw new InvalidObjectException('Cannot create a comment');
        }

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }
}

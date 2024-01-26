<?php

namespace QuizBundle\Hydrator;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;

class Tiebreaker extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('name', 'correct_answer');

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
            throw new InvalidObjectException('Cannot create a quiz round in the hydrator');
        }

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }
}

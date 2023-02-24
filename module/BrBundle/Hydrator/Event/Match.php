<?php

namespace BrBundle\Hydrator\Event;

use BrBundle\Entity\Event\Match as MatchEntity;

class Match extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }
    }

    protected function doHydrate(array $array, $object = null)
    {
        // TODO: Implement doHydrate() method.
    }
}
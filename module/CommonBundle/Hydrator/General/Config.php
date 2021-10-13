<?php

namespace CommonBundle\Hydrator\General;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;

class Config extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        return $this->stdExtract($object, array('key', 'value'));
    }

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            throw new InvalidObjectException('Cannot create a config value');
        }

        return $this->stdHydrate($data, $object, array('value'));
    }
}

<?php

namespace CudiBundle\Hydrator\File;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;

class ArticleMap extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('printable');

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);

        $data['description'] = $object->getFile()->getDescription();

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            throw new InvalidObjectException('Cannot create a mapping');
        }

        if (isset($data['description'])) {
            $object->getFile()->setDescription($data['description']);
        }

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }
}

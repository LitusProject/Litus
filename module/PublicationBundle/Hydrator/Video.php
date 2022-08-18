<?php

namespace PublicationBundle\Hydrator;

use PublicationBundle\Entity\Video as VideoEntity;

class Video extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('title', 'url',);

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new VideoEntity($data['title']);
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


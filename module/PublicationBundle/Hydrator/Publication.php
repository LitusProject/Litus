<?php

namespace PublicationBundle\Hydrator;

use PublicationBundle\Entity\Publication as PublicationEntity;

class Publication extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('title', 'previewImage');

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            return new PublicationEntity($data['title']);
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

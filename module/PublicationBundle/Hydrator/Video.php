<?php

namespace PublicationBundle\Hydrator;

use PublicationBundle\Entity\Video as VideoEntity;

class Video extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('title', 'url', 'showOnHomePage');

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new VideoEntity();
        }

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);
        $data["showOnHomePage"] = $object->isShowOnHomePage();
        return $data;
    }
}


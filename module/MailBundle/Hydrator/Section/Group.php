<?php

namespace MailBundle\Hydrator\Section;

use MailBundle\Entity\Section\Group as GroupEntity;

class Group extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('name');

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new GroupEntity($data['name']);
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
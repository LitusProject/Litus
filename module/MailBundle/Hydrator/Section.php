<?php

namespace MailBundle\Hydrator;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;
use MailBundle\Entity\Section as SectionEntity;

class Section extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('name', 'attribute', 'default_value');

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $default_value = isset($data['default_value']);
            $object = new SectionEntity($data['name'], $data['attribute'], $default_value);
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
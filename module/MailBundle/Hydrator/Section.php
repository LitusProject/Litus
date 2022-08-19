<?php

namespace MailBundle\Hydrator;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;
use MailBundle\Entity\Section as SectionEntity;

class Section extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('name', 'attribute');

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new SectionEntity($data['name'], $data['attribute']);
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
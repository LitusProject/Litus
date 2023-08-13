<?php

namespace MailBundle\Hydrator;

use MailBundle\Entity\Preference as PreferenceEntity;

class Preference extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('name', 'default_value', 'attribute');

    protected function doHydrate(array $array, $object = null)
    {

        if ($object === null) {
            $object = new PreferenceEntity();
        }

        return $this->stdHydrate($array, $object, self::$stdKeys);
    }

    protected function doExtract($object = null)
    {

        if ($object === null) {
            return array();
        }

        return $this->stdExtract($object, self::$stdKeys);
    }
}
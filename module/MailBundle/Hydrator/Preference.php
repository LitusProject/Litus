<?php

namespace MailBundle\Hydrator;

use MailBundle\Entity\Preference as PreferenceEntity;

class Preference extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected function doHydrate(array $array, $object = null)
    {
        if ($object === null) {
            $object = new PreferenceEntity();
        }

        $object->setName($array['name']);
        $object->setDefaultValue(isset($array['default_value']));
        $object->setAttribute($array['attribute']);

        return $object;
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }
        $data = array();
        $data['name'] = $object->getName();
        $data['default_value'] = $object->getDefaultValue();
        $data['attribute'] = $object->getAttribute();

        return $data;
    }
}
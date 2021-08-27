<?php

namespace CommonBundle\Hydrator\General;

use CommonBundle\Entity\General\Address as AddressEntity;

class Address extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array(
        'street', 'number', 'mailbox',
        'postal', 'city',
    );

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array(
                'country' => 'BE',
            );
        }

        $data = $this->stdExtract($object, self::$stdKeys);
        $data['country'] = $object->getCountryCode();

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new AddressEntity();
        }

        return $this->stdHydrate($data, $object, array(self::$stdKeys, 'country'));
    }
}

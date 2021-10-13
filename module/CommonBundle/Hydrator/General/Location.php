<?php

namespace CommonBundle\Hydrator\General;

use CommonBundle\Entity\General\Location as LocationEntity;

class Location extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $geoKeys = array(
        'latitude', 'longitude',
    );

    private static $stdKeys = array('name');

    protected function doExtract($object = null)
    {
        $hydrator = $this->getHydrator('CommonBundle\Hydrator\General\Address');

        if ($object === null) {
            return array(
                'address' => $hydrator->extract(null),
            );
        }

        $data = $this->stdExtract($object, array('name'));

        $data['geographical'] = $this->stdExtract($object, self::$geoKeys);
        $data['address'] = $hydrator->extract($object->getAddress());

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new LocationEntity();
        }

        $hydrator = $this->getHydrator('CommonBundle\Hydrator\General\Address');

        if (isset($data['address'])) {
            $object->setAddress(
                $hydrator->hydrate($data['address'], $object->getAddress())
            );
        }

        if (isset($data['geographical'])) {
            $this->stdHydrate($data['geographical'], $object, self::$geoKeys);
        }

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }
}

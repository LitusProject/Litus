<?php

namespace CommonBundle\Hydrator\General;

use CommonBundle\Entity\General\Location as LocationEntity;

class Location extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $geo_keys = array(
        'latitude', 'longitude',
    );

    private static $std_keys = array('name');

    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array(
                'address' => $this->getHydrator('CommonBundle\Hydrator\General\Address')
                        ->extract(null),
            );
        }

        $data = $this->stdExtract($object, array('name'));

        $data['geographical'] = $this->stdExtract($object, self::$geo_keys);

        $data['address'] = $this->getHydrator('CommonBundle\Hydrator\General\Address')
            ->extract($object->getAddress());

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            $object = new LocationEntity();
        }

        if (isset($data['address'])) {
            $object->setAddress(
                $this->getHydrator('CommonBundle\Hydrator\General\Address')
                    ->hydrate($data['address'], $object->getAddress())
            );
        }

        if (isset($data['geographical'])) {
            $this->stdHydrate($data['geographical'], $object, self::$geo_keys);
        }

        return $this->stdHydrate($data, $object, self::$std_keys);
    }
}

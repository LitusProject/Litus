<?php

namespace CudiBundle\Hydrator;

use CudiBundle\Entity\Supplier as SupplierEntity;

class Supplier extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array(
        'name', 'phone_number', 'vat_number', 'template', 'contact',
    );

    protected function doExtract($object = null)
    {
        $hydratorAddress = $this->getHydrator('CommonBundle\Hydrator\General\Address');

        if ($object === null) {
            return array(
                'address' => $hydratorAddress->extract(null),
            );
        }

        $data = $this->stdExtract($object, self::$stdKeys);
        $data['address'] = $hydratorAddress->extract($object->getAddress());

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new SupplierEntity();
        }

        $hydratorAddress = $this->getHydrator('CommonBundle\Hydrator\General\Address');

        $object->setAddress(
            $hydratorAddress->hydrate($data['address'], $object->getAddress())
        );

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }
}

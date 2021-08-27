<?php

namespace CudiBundle\Hydrator\User\Person;

use CudiBundle\Entity\User\Person\Supplier as SupplierEntity;

class Supplier extends \CommonBundle\Hydrator\User\Person
{
    protected function doHydrate(array $data, $object = null)
    {
        $data['roles'] = array('supplier');

        if ($object === null) {
            $object = new SupplierEntity();
            $object->setUsername($data['username']);
        }

        return parent::doHydrate($data, $object);
    }
}

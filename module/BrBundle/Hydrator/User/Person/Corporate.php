<?php

namespace BrBundle\Hydrator\User\Person;

use BrBundle\Entity\User\Person\Corporate as CorporateEntity;

class Corporate extends \CommonBundle\Hydrator\User\Person
{
    protected function doHydrate(array $data, $object = null)
    {
        $data['roles'] = array('corporate');

        if ($object === null) {
            $object = new CorporateEntity();
            $object->setUsername($data['username']);
        }

        return parent::doHydrate($data, $object);
    }
}

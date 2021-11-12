<?php

namespace SyllabusBundle\Hydrator;

use SyllabusBundle\Entity\Poc as PocEntity;

class Poc extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array();

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new PocEntity();
        }

        $object->setAcademic(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findOneById($data['person']['id'])
        );

        return $object;
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        return $this->stdExtract($object, self::$stdKeys);
    }
}

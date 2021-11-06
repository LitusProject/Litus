<?php

namespace FormBundle\Hydrator;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;

class ViewerMap extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('edit', 'mail');

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            throw new InvalidObjectException('Cannot create a viewer map');
        }

        $object = $this->stdHydrate($data, $object, self::$stdKeys);

        $person = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person')
            ->findOneById($data['person']['id']);

        $object->setPerson($person);

        return $object;
    }

    protected function doExtract($object = null)
    {
        return array();
    }
}

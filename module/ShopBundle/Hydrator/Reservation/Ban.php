<?php

namespace ShopBundle\Hydrator\Reservation;

use ShopBundle\Entity\Reservation\Ban as BanEntity;

class Ban extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected function doExtract($object = null)
    {
        return array();
    }

    protected function doHydrate(array $data, $object = null)
    {
        error_log("hydrate");

        if ($object === null) {
            $object = new BanEntity();
        }

        $object->setStartTimestamp(self::loadDateTime($data['start_timestamp']));
        $object->setEndTimestamp(self::loadDateTime($data['end_timestamp']));

        $person = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person')
            ->find($data['person']['id']);
        $object->setPerson($person);

        return $object;
    }
}

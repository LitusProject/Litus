<?php
namespace BrBundle\Hydrator\Event;

use BrBundle\Entity\Event as EventEntity;
use BrBundle\Entity\Event\Subscription as SubscriptionEntity;

class Subscription extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('first_name', 'last_name', 'email', 'phone_number','specialization', 'network_reception', 'university', 'study', 'study_year', 'food', 'network_reception', 'consent', 'event');
    //TODO(Tom): event is een missing var, idk wrm

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }
        $data = $this->stdExtract($object, self::$stdKeys);
        $data["network_reception"] = $object->isAtNetworkReception();
        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new SubscriptionEntity();
        }

        $object = $this->stdHydrate($data, $object, self::$stdKeys);
        $object->setAtNetworkReception($data["network_reception"]);
        return $object;
    }
}
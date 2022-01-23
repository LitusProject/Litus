<?php
namespace BrBundle\Hydrator\Event;

use BrBundle\Entity\Event as EventEntity;
use BrBundle\Entity\Event\Subscription as SubscriptionEntity;

class Subscription extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('first_name', 'last_name', 'email', 'phone_number','specialization', 'network_reception');

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }
        $data = $this->stdExtract($object, self::$stdKeys);

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new SubscriptionEntity();
        }

        $object = $this->stdHydrate($data, $object, self::$stdKeys);

        return $object;
    }
}
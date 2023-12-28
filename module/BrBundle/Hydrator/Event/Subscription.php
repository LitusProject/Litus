<?php

namespace BrBundle\Hydrator\Event;

use BrBundle\Entity\Event\Subscription as SubscriptionEntity;

class Subscription extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('first_name', 'last_name', 'email', 'phone_number','specialization', 'network_reception','university', 'study', 'other_university', 'other_study', 'study_year', 'network_reception', 'food');
    //TODO(Tom): event is een missing var, idk wrm

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }
        $data = $this->stdExtract($object, self::$stdKeys);
//        $data['network_reception'] = $object->isAtNetworkReception();
        // Consent should always be redone
        $data['consent'] = false;
        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new SubscriptionEntity();
        }
        $object = $this->stdHydrate($data, $object, self::$stdKeys);
//        $object->setAtNetworkReception($data['network_reception']);
        $object->setConsent($data['consent']);

        return $object;
    }
}

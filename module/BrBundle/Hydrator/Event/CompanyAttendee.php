<?php

namespace BrBundle\Hydrator\Event;

use BrBundle\Entity\Event\CompanyAttendee as CompanyAttendeeEntity;

class CompanyAttendee extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('first_name', 'last_name', 'phone_number', 'email', 'lunch', 'veggie');

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        return $this->stdExtract($object, self::$stdKeys);
    }

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new CompanyAttendeeEntity($data['companyMap']);
        }

        $object = $this->stdHydrate($data, $object, self::$stdKeys);

        return $object;
    }
}

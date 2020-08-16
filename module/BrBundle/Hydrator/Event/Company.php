<?php


namespace BrBundle\Hydrator\Event;


use BrBundle\Entity\Event as EventEntity;

class Company extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('title', 'description');

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);
        $data['start_date'] = $object->getStartDate()->format('d/m/Y H:i');
        $data['end_date'] = $object->getEndDate()->format('d/m/Y H:i');
        $date = $object->getSubscriptionDate();
        if ($date != null){
            $data['subscription_date'] = $date->format('d/m/Y H:i');
        }
        $date = $object->getMapviewDate();
        if ($date != null){
            $data['mapview_date'] = $object->getMapviewDate()->format('d/m/Y H:i');
        }
        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new EventEntity($this->getPersonEntity());
        }

        $object = $this->stdHydrate($data, $object, self::$stdKeys);

        if (isset($data['start_date'])) {
            $object->setStartDate(self::loadDateTime($data['start_date']));
        }

        if (isset($data['end_date'])) {
            $object->setEndDate(self::loadDateTime($data['end_date']));
        }

        if (isset($data['subscription_date'])) {
            $object->setSubscriptionDate(self::loadDateTime($data['subscription_date']));
        }

        if (isset($data['mapview_date'])) {
            $object->setMapviewDate(self::loadDateTime($data['mapview_date']));
        }

        return $object;
    }
}
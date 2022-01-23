<?php


namespace BrBundle\Hydrator\Event;


use BrBundle\Entity\Event as EventEntity;

class CompanyMap extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('notes','attendees');

    protected function doExtract($object = null) {
        if ($object === null) {
            return array();
        }
        $data = $this->stdExtract($object, self::$stdKeys);

        $data['master_interests'] = $object->getMasterInterests();
        $data['information_checked'] = $object->isChecked();
        return $data;
    }

    protected function doHydrate(array $data, $object = null) {
        if ($object === null) {
            return;
        }

        $object = $this->stdHydrate($data, $object, self::$stdKeys);

        $object->setMasterInterests($data['master_interests']);
        $object->setChecked($data['information_checked']);

        return $object;
    }
}
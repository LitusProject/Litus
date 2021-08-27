<?php

namespace SyllabusBundle\Hydrator;

use SyllabusBundle\Entity\Group as GroupEntity;

class Group extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('name', 'cv_book');

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new GroupEntity();
        }

        $extraMembers = preg_split('/[,;\s]+/', $data['extra_members']);
        $excludedMembers = preg_split('/[,;\s]+/', $data['excluded_members']);

        $object->setExtraMembers(serialize($extraMembers))
            ->setExcludedMembers(serialize($excludedMembers));

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $extraMembers = unserialize($object->getExtraMembers());
        $excludedMembers = unserialize($object->getExcludedMembers());

        if (!$extraMembers) {
            $extraMembers = array();
        }

        if (!$excludedMembers) {
            $excludedMembers = array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);

        $data['extra_members'] = implode(',', $extraMembers);
        $data['excluded_members'] = implode(',', $excludedMembers);

        return $data;
    }
}

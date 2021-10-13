<?php

namespace SyllabusBundle\Hydrator;

use SyllabusBundle\Entity\Subject as SubjectEntity;

class Subject extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('code', 'name', 'semester', 'credits');

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new SubjectEntity();
        }

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        return $this->stdExtract($object, self::$stdKeys);
    }
}

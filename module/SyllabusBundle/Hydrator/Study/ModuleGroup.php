<?php

namespace SyllabusBundle\Hydrator\Study;

use SyllabusBundle\Entity\Study\ModuleGroup as ModuleGroupEntity;

class ModuleGroup extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('external_id', 'title', 'phase', 'language', 'mandatory');

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new ModuleGroupEntity();
        }

        if (isset($data['parent']['id'])) {
            $parent = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\Study\ModuleGroup')
                ->findOneById($data['parent']['id']);

            $object->setParent($parent);
        } else {
            $object->setParent(null);
        }

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);

        $parent = $object->getParent();
        if ($parent !== null) {
            $data['parent'] = array(
                'id'    => $parent->getId(),
                'value' => 'Phase ' . $parent->getPhase() . ' - ' . $parent->getTitle(),
            );
        }

        return $data;
    }
}

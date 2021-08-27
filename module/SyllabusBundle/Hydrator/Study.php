<?php

namespace SyllabusBundle\Hydrator;

use SyllabusBundle\Entity\Study as StudyEntity;
use SyllabusBundle\Entity\Study\Combination as CombinationEntity;

class Study extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array();

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new StudyEntity();
        }

        $combination = $object->getCombination();
        if ($combination === null) {
            $combination = new CombinationEntity();
            $object->setCombination($combination);
        }

        $combination->setTitle($data['title'])
            ->setExternalId($data['external_id'])
            ->setPhase($data['phase']);

        $groups = array();
        foreach ($data['module_groups'] as $groupData) {
            if ($groupData['module_group']['value'] == '') {
                continue;
            }

            $group = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\Study\ModuleGroup')
                ->findOneById($groupData['module_group']['id']);

            if ($group !== null) {
                $groups[] = $group;
            }
        }

        $combination->setModuleGroups($groups);

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);

        $data['title'] = $object->getTitle();
        $data['external_id'] = $object->getCombination()->getExternalId();
        $data['phase'] = $object->getPhase();

        $data['module_groups'] = array();

        foreach ($object->getCombination()->getModuleGroups() as $group) {
            $data['module_groups'][] = array(
                'module_group' => array(
                    'id'    => $group->getId(),
                    'value' => 'Phase ' . $group->getPhase() . ' - ' . $group->getTitle(),
                ),
            );
        }

        return $data;
    }
}

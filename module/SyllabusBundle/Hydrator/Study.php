<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace SyllabusBundle\Hydrator;

use SyllabusBundle\Entity\Study as StudyEntity,
    SyllabusBundle\Entity\Study\Combination as CombinationEntity;

class Study extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array();

    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            $object = new StudyEntity();
        }

        $combination = $object->getCombination();
        if (null === $combination) {
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

            if (null !== $group) {
                $groups[] = $group;
            }
        }

        $combination->setModuleGroups($groups);

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }

    protected function doExtract($object = null)
    {
        if (null === $object) {
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

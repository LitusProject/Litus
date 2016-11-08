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
 *
 * @license http://litus.cc/LICENSE
 */

namespace SyllabusBundle\Hydrator;

use SyllabusBundle\Entity\Group as GroupEntity;

class Group extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('name', 'cv_book');

    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
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
        if (null === $object) {
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

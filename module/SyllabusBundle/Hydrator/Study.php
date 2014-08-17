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

use SyllabusBundle\Entity\Study as StudyEntity;

class Study extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $std_keys = array('kul_id', 'title', 'phase', 'language');

    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            $object = new StudyEntity;
        }

        $object->setParent(
            $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\Study')
                ->findOneById($data['parent_id'])
        );

        return $this->stdHydrate($data, $object, self::$std_keys);
    }

    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array();
        }

        $data = $this->stdExtract($object, self::$std_keys);

        $data['parent_id'] = $object->getParent() ? $object->getParent()->getId() : '';
        $data['parent'] = $object->getParent() ? $object->getParent()->getFullTitle() : '';

        return $data;
    }
}

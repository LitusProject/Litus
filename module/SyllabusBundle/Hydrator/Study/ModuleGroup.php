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

namespace SyllabusBundle\Hydrator\Study;

use SyllabusBundle\Entity\Study\ModuleGroup as ModuleGroupEntity;

class ModuleGroup extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('external_id', 'title', 'phase', 'language', 'mandatory');

    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
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
        if (null === $object) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);

        if ($parent = $object->getParent()) {
            $data['parent'] = array(
                'id' => $parent->getId(),
                'value' => 'Phase ' . $parent->getPhase() . ' - ' . $parent->getTitle(),
            );
        }

        return $data;
    }
}

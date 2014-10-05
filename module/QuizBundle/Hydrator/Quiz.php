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

namespace QuizBundle\Hydrator;

use QuizBundle\Entity\Quiz as QuizEntity;

class Quiz extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $std_keys = array('name');

    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array();
        }

        $data = $this->stdExtract($object, self::$std_keys);

        $data['edit_roles'] = array();
        foreach ($object->getEditRoles() as $role) {
            $data['edit_roles'][] = $role->getName();
        }

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            $object = new QuizEntity($this->getPerson());
        }

        if (isset($data['edit_roles'])) {
            $editRoles = array();

            foreach ($data['edit_roles'] as $editRole) {
                $editRoles[] = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\Acl\Role')
                    ->findOneByName($editRole);
            }

            $object->setEditRoles($editRoles);
        }

        return $this->stdHydrate($data, $object, self::$std_keys);
    }
}

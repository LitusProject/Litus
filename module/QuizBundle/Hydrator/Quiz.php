<?php

namespace QuizBundle\Hydrator;

use QuizBundle\Entity\Quiz as QuizEntity;

class Quiz extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('name');

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);

        $data['edit_roles'] = array();
        foreach ($object->getEditRoles() as $role) {
            $data['edit_roles'][] = $role->getName();
        }

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new QuizEntity($this->getPersonEntity());
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

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }
}

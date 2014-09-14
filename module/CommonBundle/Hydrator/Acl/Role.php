<?php

namespace CommonBundle\Hydrator\Acl;

use CommonBundle\Entity\Acl\Action as ActionEntity,
    CommonBundle\Entity\Acl\Role as RoleEntity;

class Role extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array();
        }

        return array(
            'actions' => $this->actionsToArray($object->getActions()),
            'name'    => $object->getName(),
            'parents' => $this->rolesToArray($object->getParents()),
        );
    }

    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            $object = new RoleEntity($data['name']);
        }

        if (isset($data['actions'])) {
            $object->setActions($this->arrayToActions($data['actions']));
        }

        if (isset($data['parents'])) {
            $object->setParents($this->arrayToRoles($data['parents']));
        }

        return $object;
    }

    /**
     * @param RoleEntity[] $roles
     * @return string[]
     */
    protected function rolesToArray(array $roles) {
        return array_map(function ($role) {
            return $role->getName();
        }, $roles);
    }

    /**
     * @param string[]
     * @return RoleEntity[]
     */
    protected function arrayToRoles(array $roles) {
        return array_map(function ($role) {
            return $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\Acl\Role')
                ->findOneByName($role);
        }, $roles);
    }

    /**
     * @param ActionEntity[]
     * @return string[]
     */
    protected function actionsToArray(array $actions) {
        return array_map(function ($action) {
            return $action->getId();
        }, $actions);
    }

    /**
     * @param string[]
     * @return ActionEntity[]
     */
    protected function arrayToActions(array $actions) {
        return array_map(function ($action) {
            return $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\Acl\Action')
                ->findOneById($action);
        }, $actions);
    }
}

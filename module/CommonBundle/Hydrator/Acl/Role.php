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

namespace CommonBundle\Hydrator\Acl;

use CommonBundle\Entity\Acl\Action as ActionEntity;
use CommonBundle\Entity\Acl\Role as RoleEntity;

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
     * @param  RoleEntity[] $roles
     * @return string[]
     */
    protected function rolesToArray(array $roles)
    {
        return array_map(
            function ($role) {
                return $role->getName();
            },
            $roles
        );
    }

    /**
     * @param string[]
     * @return RoleEntity[]
     */
    protected function arrayToRoles(array $roles)
    {
        return array_map(
            function ($role) {
                return $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\Acl\Role')
                    ->findOneByName($role);
            },
            $roles
        );
    }

    /**
     * @param ActionEntity[]
     * @return string[]
     */
    protected function actionsToArray(array $actions)
    {
        return array_map(
            function ($action) {
                return $action->getId();
            },
            $actions
        );
    }

    /**
     * @param string[]
     * @return ActionEntity[]
     */
    protected function arrayToActions(array $actions)
    {
        return array_map(
            function ($action) {
                return $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\Acl\Action')
                    ->findOneById($action);
            },
            $actions
        );
    }
}

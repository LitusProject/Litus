<?php

namespace CommonBundle\Hydrator\General\Organization;

use CommonBundle\Entity\General\Organization\Unit as UnitEntity;

class Unit extends CommonBundle\Component\Hydrator\Hydrator
{
    private static $std_keys = array('name', 'mail', 'displayed');

    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array();
        }

        $data = $this->stdExtract($object, self::$std_keys);

        $data['organization'] = $object->getOrganization()->getId();

        if (null !== $object->getParent()) {
            $data['parent'] = $object->getParent()->getId();
        }

        $data['roles'] = $this->rolesToArray($object->getRoles(false));
        $data['coordinator_roles'] = $this->rolesToArray($object->getCoordinatorRoles(false));

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            $object = new UnitEntity();
        }

        if (isset($data['organization'])) {
            $organization = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Organization')
                ->findOneById($data['organization']);
        } else {
            $organization = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Organization')
                ->findOne();
        }

        $parent = null;
        if (isset($data['parent']) && ('' != $data['parent'])) {
            $parent = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Organization\Unit')
                ->findOneById($data['parent']);
        }

        $object->setOrganization($organization)
            ->setParent($parent);

        if (isset($data['roles'])) {
            $object->setRoles($this->arrayToRoles($data['roles']));
        }

        if (isset($data['coordinator_roles'])) {
            $object->setCoordinatorRoles($this->arrayToRoles($data['coordinator_roles']));
        }

        return $this->stdHydrate($data, $object, self::$std_keys);
    }

    protected function rolesToArray(array $roles)
    {
        return array_filter(array_map(function ($role) {
            if ($role->getSystem()) {
                return null;
            }

            return $role->getName();
        }, $roles));
    }

    protected function arrayToRoles(array $roles)
    {
        return array_map(function ($role) {
            return $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\Acl\Role')
                ->findOneByName($role);
        }, $roles);
    }
}

<?php

namespace CommonBundle\Hydrator\General\Organization;

use CommonBundle\Entity\General\Organization\Unit as UnitEntity;

class Unit extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('name', 'mail', 'displayed', 'workgroup');

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);

        $data['organization'] = $object->getOrganization()->getId();

        if ($object->getParent() !== null) {
            $data['parent'] = $object->getParent()->getId();
        }

        $data['roles'] = $this->rolesToArray($object->getRoles(false));
        $data['coordinator_roles'] = $this->rolesToArray($object->getCoordinatorRoles(false));

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
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
        if (isset($data['parent']) && ($data['parent'] != '')) {
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

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }

    protected function rolesToArray(array $roles)
    {
        return array_filter(
            array_map(
                function ($role) {
                    if ($role->getSystem()) {
                        return null;
                    }

                    return $role->getName();
                },
                $roles
            )
        );
    }

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
}

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

namespace CommonBundle\Hydrator\General\Organization;

use CommonBundle\Entity\General\Organization\Unit as UnitEntity;

class Unit extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('name', 'mail', 'displayed');

    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);

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

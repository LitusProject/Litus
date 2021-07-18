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

namespace CommonBundle\Hydrator\User;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;
use CommonBundle\Entity\User\Barcode\Ean12 as Barcode;
use CommonBundle\Entity\User\Status\Organization as OrganizationStatus;

abstract class Person extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected static $stdKeys = array(
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'sex',
    );

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $organizationYear = $this->getCurrentAcademicYear(true);

        $data = $this->stdExtract($object, array(self::$stdKeys, 'username'));

        $data['roles'] = $this->rolesToData($object->getRoles());
        $data['system_roles'] = $this->rolesToData($object->getSystemRoles());

        $data['organization'] = array(
            'barcode' => $object->getBarcode() !== null ? $object->getBarcode()->getBarcode() : '',
            'status' => $object->getOrganizationStatus($organizationYear) !== null ? $object->getOrganizationStatus($organizationYear)->getStatus() : null,
        );

        $data['code'] = $object->getCode() !== null ? $object->getCode()->getCode() : null;

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            throw new InvalidObjectException('Cannot create a person');
        }

        if (isset($data['roles'])) {
            $object->setRoles(array_unique(array_merge($this->dataToRoles($data['roles']), $object->getSystemRoles())));
        } else {
            $object->setRoles($this->dataToRoles($object->getSystemRoles()));
        }

        if (isset($data['organization'])) {
            $organizationYear = $this->getCurrentAcademicYear(true);

            if ($data['organization']['status'] != '') {
                if ($object->getOrganizationStatus($organizationYear) !== null) {
                    $object->getOrganizationStatus($organizationYear)
                        ->setStatus($data['organization']['status']);
                } else {
                    $object->addOrganizationStatus(
                        new OrganizationStatus(
                            $object,
                            $data['organization']['status'],
                            $organizationYear
                        )
                    );
                }
            } else {
                if ($object->getOrganizationStatus($organizationYear) !== null) {
                    $status = $object->getOrganizationStatus($organizationYear);

                    $object->removeOrganizationStatus($status);
                    $this->getEntityManager()->remove($status);
                }
            }

            if ($data['organization']['barcode'] != '') {
                $code = $data['organization']['barcode'];
                if ($object->getBarcode() !== null) {
                    if ($object->getBarcode()->getBarcode() != $code) {
                        $object->addBarcode(
                            new Barcode($object, $code)
                        );
                    }
                } else {
                    $object->addBarcode(new Barcode($object, $code));
                }
            }
        }

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }

    protected function dataToRoles($rolesData)
    {
        $roles = array();

        foreach ($rolesData as $role) {
            $roles[] = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\Acl\Role')
                ->findOneByName($role);
        }

        return $roles;
    }

    protected function rolesToData($roles)
    {
        $rolesData = array();

        foreach ($roles as $role) {
            $rolesData[] = $role->getName();
        }

        return $rolesData;
    }
}

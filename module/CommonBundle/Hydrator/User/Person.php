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

namespace CommonBundle\Hydrator\User;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;
use CommonBundle\Entity\User\Status\Organization as OrganizationStatus;

abstract class Person extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $std_keys = array(
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'sex',
    );

    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array();
        }

        $academicYear = $this->getCurrentAcademicYear();

        $data = $this->stdExtract($object, array(self::$std_keys, 'username'));

        $data['roles'] = $this->rolesToData($object->getRoles());
        $data['system_roles'] = $this->rolesToData($object->getSystemRoles());

        $data['organization'] = array(
            'barcode' => null !== $object->getBarcode()
                    ? $object->getBarcode()->getBarcode()
                    : '',
            'status'  => null !== $object->getOrganizationStatus($academicYear)
                    ? $object->getOrganizationStatus($academicYear)->getStatus()
                    : null,
        );

        $data['code'] = null !== $object->getCode()
            ? $code->getCode()->getCode()
            : null;

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            throw new InvalidObjectException('Cannot create a person');
        }

        if (isset($data['roles'])) {
            $object->setRoles($this->dataToRoles($data['roles']));
        }

        if (isset($data['organization'])) {
            $academicYear = $this->getCurrentAcademicYear();

            if ('' != $data['organization']['status']) {
                if (null !== $object->getOrganizationStatus($academicYear)) {
                    $object->getOrganizationStatus($academicYear)
                        ->setStatus($data['organization']['status']);
                } else {
                    $object->addOrganizationStatus(
                        new OrganizationStatus(
                            $object,
                            $data['organization']['status'],
                            $academicYear
                        )
                    );
                }
            } else {
                if (null !== $object->getOrganizationStatus($academicYear)) {
                    $status = $object->getOrganizationStatus($academicYear);

                    $object->removeOrganizationStatus($status);
                    $this->getEntityManager()->remove($status);
                }
            }

            if ('' != $data['organization']['barcode']) {
                $code = $data['organization']['barcode'];
                if (null !== $object->getBarcode()) {
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

        return $this->stdHydrate($data, $object, self::$std_keys);
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

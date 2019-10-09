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

namespace CommonBundle\Hydrator\User\Person;

use CommonBundle\Entity\User\Person\Academic as AcademicEntity;
use CommonBundle\Entity\User\Status\University as UniversityStatus;

class Academic extends \CommonBundle\Hydrator\User\Person
{
    protected static $stdKeys = array(
        'university_identification', 'personal_email', 'is_international',
    );

    public function doExtract($object = null)
    {
        $data = parent::doExtract($object);

        if ($object === null) {
            return $data;
        }

        $hydratorAddress = $this->getHydrator('CommonBundle\Hydrator\General\Address');
        $hydratorPrimaryAddress = $this->getHydrator('CommonBundle\Hydrator\General\PrimaryAddress');

        $data = array_merge(
            $data,
            array(
                'roles'             => $this->rolesToData($object->getRoles(false)),
                'primary_email'     => $object->getEmail() === $object->getPersonalEmail(),
                'is_international'  => $object->isInternational(),
                'birthday'          => $object->getBirthday() !== null ? $object->getBirthday()->format('d/m/Y') : '',
                'secondary_address' => $hydratorAddress->extract($object->getSecondaryAddress()),
                'primary_address'   => $hydratorPrimaryAddress->extract($object->getPrimaryAddress()),
            )
        );

        $data = array_merge(
            $data,
            $this->stdExtract($object, self::$stdKeys)
        );

        $academicYear = $this->getCurrentAcademicYear();

        $data['university'] = array(
            'email'          => explode('@', $object->getUniversityEmail())[0],
            'identification' => $data['university_identification'],
            'status'         => $object->getUniversityStatus($academicYear) !== null ? $object->getUniversityStatus($academicYear)->getStatus() : null,
        );

        if (isset($data['organization'])) {
            $data['organization']['is_in_workinggroup'] = $object->isInWorkingGroup();
        } else {
            $data['organization'] = array(
                'is_in_workinggroup' => $object->isInWorkingGroup(),
            );
        }

        $data['unit_roles'] = $this->rolesToData($object->getUnitRoles());

        return $data;
    }

    public function doHydrate(array $data, $object = null)
    {
        $academicYear = $this->getCurrentAcademicYear();

        if ($object === null) {
            $object = new AcademicEntity();
            if (isset($data['username']) && $data['username'] != '') {
                $object->setUsername($data['username']);
            } else {
                $object->setUsername($data['university']['identification']);
            }

            $object->setRoles(
                array(
                    $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\Acl\Role')
                        ->findOneByName('guest'),
                    $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\Acl\Role')
                        ->findOneByName('student'),
                )
            );
        }

        if (isset($data['university']['status']) && $data['university']['status'] != '') {
            if ($object->getUniversityStatus($academicYear) !== null) {
                $object->getUniversityStatus($academicYear)
                    ->setStatus($data['university']['status']);
            } else {
                $object->addUniversityStatus(
                    new UniversityStatus(
                        $object,
                        $data['university']['status'],
                        $academicYear
                    )
                );
            }
        } else {
            $status = $object->getUniversityStatus($academicYear);
            if ($status !== null) {
                $object->removeUniversityStatus(
                    $object->getUniversityStatus($academicYear)
                );
            }
        }

        $studentDomain = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('student_email_domain');

        $universityEmail = preg_replace('/[^a-z0-9\.@]/i', '', iconv('UTF-8', 'US-ASCII//TRANSLIT', $data['university']['email'])) . $studentDomain;
        if (isset($data['primary_email']) && $data['primary_email']) {
            $data['email'] = $data['personal_email'] ?? $object->getPersonalEmail();
        } else {
            $data['email'] = $universityEmail;
        }

        if (isset($data['organization']) && isset($data['organization']['is_in_workinggroup']) && $data['organization']['is_in_workinggroup']) {
            $object->setIsInWorkingGroup($data['organization']['is_in_workinggroup']);
        }

        if (isset($data['birthday']) && $data['birthday'] != '') {
            $object->setBirthday(self::loadDate($data['birthday']));
        }

        $object->setUniversityEmail($universityEmail)
            ->setUniversityIdentification($data['university']['identification']);

        $hydratorAddress = $this->getHydrator('CommonBundle\Hydrator\General\Address');
        if (isset($data['secondary_address']) && isset($data['secondary_address']['city']) && $data['secondary_address']['city'] != '') {
            $object->setSecondaryAddress(
                $hydratorAddress->hydrate($data['secondary_address'], $object->getSecondaryAddress())
            );
        }

        $hydratorPrimaryAddress = $this->getHydrator('CommonBundle\Hydrator\General\PrimaryAddress');
        if (isset($data['primary_address']) && isset($data['primary_address']['city']) && $data['primary_address']['city'] != '') {
            $object->setPrimaryAddress(
                $hydratorPrimaryAddress->hydrate($data['primary_address'], $object->getPrimaryAddress())
            );
        }

        parent::doHydrate($data, $object);

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }
}

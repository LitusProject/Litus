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

namespace CommonBundle\Hydrator\User\Person;

use CommonBundle\Entity\User\Person\Academic as AcademicEntity,
    CommonBundle\Entity\User\Status\University as UniversityStatus;

class Academic extends \CommonBundle\Hydrator\User\Person
{
    protected static $stdKeys = array(
        'university_identification', 'personal_email',
    );

    public function doExtract($object = null)
    {
        $data = parent::doExtract($object);

        if (null === $object) {
            return $data;
        }

        /** @var \CommonBundle\Hydrator\General\Address $hydratorAddress */
        $hydratorAddress = $this->getHydrator('CommonBundle\Hydrator\General\Address');

        /** @var \CommonBundle\Hydrator\General\PrimaryAddress $hydratorPrimaryAddress */
        $hydratorPrimaryAddress = $this->getHydrator('CommonBundle\Hydrator\General\PrimaryAddress');

        $data['roles'] = $this->rolesToData($object->getRoles(false));

        $data['primary_email'] = $object->getEmail() === $object->getPersonalEmail();

        $data['birthday'] = $object->getBirthday() !== null
            ? $object->getBirthday()->format('d/m/Y')
            : '';

        $data['secondary_address'] = $hydratorAddress->extract($object->getSecondaryAddress());
        $data['primary_address'] = $hydratorPrimaryAddress->extract($object->getPrimaryAddress());

        $data = array_merge(
            $data,
            $this->stdExtract($object, self::$stdKeys)
        );

        $academicYear = $this->getCurrentAcademicYear();

        $data['university'] = array(
            'email'          => explode('@', $object->getUniversityEmail())[0],
            'identification' => $data['university_identification'],
            'status'         => null !== $object->getUniversityStatus($academicYear)
                    ? $object->getUniversityStatus($academicYear)->getStatus()
                    : null,
        );

        $data['unit_roles'] = $this->rolesToData($object->getUnitRoles());

        return $data;
    }

    public function doHydrate(array $data, $object = null)
    {
        $academicYear = $this->getCurrentAcademicYear();

        if (null === $object) {
            $object = new AcademicEntity();
            if (isset($data['username'])) {
                $object->setUsername($data['username']);
            } else {
                $object->setUsername($data['university']['identification']);
            }

            $object->setRoles(array(
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\Acl\Role')
                    ->findOneByName('guest'),
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\Acl\Role')
                    ->findOneByName('student'),
            ));
        }

        if (!empty($data['university']['status'])) {
            if (null !== $object->getUniversityStatus($academicYear)) {
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
            if (null !== $status) {
                $object->removeUniversityStatus(
                    $object->getUniversityStatus($academicYear)
                );
            }
        }

        $studentDomain = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('student_email_domain');

        $universityEmail = preg_replace('/[^a-z0-9\.@]/i', '', iconv('UTF-8', 'US-ASCII//TRANSLIT', $data['university']['email'])) . $studentDomain;
        if (isset($data['primary_email'])) {
            if ($data['primary_email']) {
                $data['email'] = $data['personal_email'];
            } else {
                $data['email'] = $universityEmail;
            }
        }

        if (isset($data['birthday'])) {
            $object->setBirthday(self::loadDate($data['birthday']));
        }
        $object->setUniversityEmail($universityEmail)
            ->setUniversityIdentification($data['university']['identification']);

        /** @var \CommonBundle\Hydrator\General\Address $hydratorAddress */
        $hydratorAddress = $this->getHydrator('CommonBundle\Hydrator\General\Address');

        /** @var \CommonBundle\Hydrator\General\PrimaryAddress $hydratorPrimaryAddress */
        $hydratorPrimaryAddress = $this->getHydrator('CommonBundle\Hydrator\General\PrimaryAddress');

        if (isset($data['secondary_address']) && is_array($data['secondary_address']) && !empty($data['secondary_address']['city'])) {
            $object->setSecondaryAddress(
                $hydratorAddress->hydrate($data['secondary_address'], $object->getSecondaryAddress())
            );
        }

        if (isset($data['primary_address']) && is_array($data['primary_address']) && !empty($data['primary_address']['city'])) {
            $object->setPrimaryAddress(
                $hydratorPrimaryAddress->hydrate($data['primary_address'], $object->getPrimaryAddress())
            );
        }

        parent::doHydrate($data, $object);

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }
}

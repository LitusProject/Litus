<?php

namespace CommonBundle\Hydrator\User\Person;

use CommonBundle\Entity\User\Person\Academic as AcademicEntity;
use CommonBundle\Entity\User\Status\University as UniversityStatus;

class Academic extends \CommonBundle\Hydrator\User\Person
{
    protected static $stdKeys = array(
        'university_identification', 'personal_email', 'is_international', 'no_mail',
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
        $data['preference_mappings'] = $object->getPreferenceMappings();
        $data['email_address_preference'] = $object->getEmailAddressPreference();
        $data['unsubscribed'] = $object->getUnsubscribed();

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
        error_log($object->isInWorkingGroup($academicYear));
        if (isset($data['organization'])) {
            $data['organization']['is_in_workinggroup'] = $object->isInWorkingGroup($academicYear);
        } else {
            $data['organization'] = array(
                'is_in_workinggroup' => $object->isInWorkingGroup($academicYear),
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
                $object->setUsername(strtolower($data['username']));
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

        if (isset($data['preference_mappings'])) {
            foreach ($data['preference_mappings'] as $preferenceMapping) {
                $object->addPreferenceMapping($preferenceMapping);
            }
        }

        if (isset($data['email_address_preference'])) {
            $object->setEmailAddressPreference($data['email_address_preference']);
        }

        if (isset($data['unsubscribed'])) {
            $object->setUnsubscribed($data['unsubscribed']);
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

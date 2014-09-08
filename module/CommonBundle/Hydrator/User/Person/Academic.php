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

use CommonBundle\Entity\User\Person\Academic as AcademicEntity;

class Academic extends \CommonBundle\Hydrator\User\Person
{
    private static $std_keys = array(
        'university_identification', 'personal_email',
    );

    public function doExtract($object = null)
    {
        $data = parent::doExtract($object);

        if (null === $object) {
            return $data;
        }

        $data['roles'] = $this->rolesToData($object->getRoles(false));

        $data['primary_email'] = $object->getEmail() === $object->getPersonalEmail();
        $data['university_email'] = explode('@', $object->getUniversityEmail())[0];
        $data['birthday'] = $object->getBirthday() !== null
            ? $object->getBirthday()->format('d/m/Y')
            : '';

        $data['secondary_address'] = $this->getHydrator('CommonBundle\Hydrator\General\Address')
            ->extract($object->getSecondaryAddress());
        $data['primary_address'] = $this->getHydrator('CommonBundle\Hydrator\General\PrimaryAddress')
            ->extract($object->getPrimaryAddress());

        return array_merge(
            $data,
            $this->stdExtract($object, self::$std_keys)
        );;
    }

    public function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            $object = new AcademicEntity();
            $object->setUsername($data['university_identification']);

            $object->setRoles(array(
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\Acl\Role')
                    ->findOneByName('guest'),
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\Acl\Role')
                    ->findOneByName('student'),
            ));
        }

        $studentDomain = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('student_email_domain');

        $universityEmail = preg_replace('/[^a-z0-9\.@]/i', '', iconv("UTF-8", "US-ASCII//TRANSLIT", $data['university_email'])).$studentDomain;

        if (isset($data['primary_email'])) {
            if ($data['primary_email']) {
                $data['email'] = $data['personal_email'];
            } else {
                $data['email'] = $universityEmail;
            }
        }

        $object->setBirthday(self::loadDate($data['birthday']))
            ->setUniversityEmail($universityEmail);

        if (!isset($data['secondary_address'])) {
            $data['secondary_address'] = array();
        }
        $object->setSecondaryAddress(
            $this->getHydrator('CommonBundle\Hydrator\General\Address')
                ->hydrate($data['secondary_address'], $object->getSecondaryAddress())
        );

        if (!isset($data['primary_address'])) {
            $data['primary_address'] = array();
        }
        $object->setPrimaryAddress(
            $this->getHydrator('CommonBundle\Hydrator\General\PrimaryAddress')
                ->hydrate($data['primary_address'], $object->getPrimaryAddress())
        );

        parent::doHydrate($data, $object);

        return $this->stdHydrate($data, $object, self::$std_keys);
    }
}

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

namespace SecretaryBundle\Hydrator\Organization;

use CommonBundle\Entity\User\Status\University as UniversityStatus;
use SecretaryBundle\Entity\Organization\MetaData as MetaDataEntity;

class MetaData extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $std_keys = array(
        'tshirt_size',
    );

    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array();
        }

        $data = array(
            'academic'          => $this->getHydrator('CommonBundle\Hydrator\User\Person\Academic')
                ->extract($object->getAcademic()),
            'organization_info' => $this->stdExtract($object, self::$std_keys),
        );

        $data['organization_info']['receive_irreeel_at_cud'] = $object->receiveIrreeelAtCudi();
        $data['organization_info']['become_member'] = $object->becomeMember();
        $data['organization_info']['bakske_by_mail'] = $object->bakskeByMail();

        // sure thing, if we're here, the user already checked the conditions
        $data['organization_info']['conditions'] = true;

        $organization = $object->getAcademic()
            ->getOrganization($this->getCurrentAcademicYear(true));
        $data['organization_info']['organization'] = $organization !== null ? $organization->getId() : 0;

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        $year = $this->getCurrentAcademicYear(false);

        if (null === $object) {
            if (!isset($data['academic'])) {
                throw new LogicException('Cannot create a MetaData without Academic.');
            }

            $academic = $data['academic'];

            $academicEntity = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findOneByUniversityIdentification($academic['university_identification']);

            $academic = $this->getHydrator('CommonBundle\Hydrator\User\Person\Academic')
                ->hydrate($academic, $academicEntity);

            $object = new MetaDataEntity($academic, $year);
        } else {
            if (isset($data['academic'])) {
                $this->getHydrator('CommonBundle\Hydrator\User\Person\Academic')
                    ->hydrate($data['academic'], $object->getAcademic());
            }
        }

        $academic = $object->getAcademic();

        $data = $data['organization_info'];

        if ($academic->canHaveUniversityStatus($year)) {
            $academic->addUniversityStatus(
                new UniversityStatus(
                    $academic,
                    'student',
                    $year
                )
            );
        }

        if ($data['become_member']) {
            $this->stdHydrate($data, $object, array('become_member', 'tshirt_size', 'receive_irreeel_at_cudi', 'bakske_by_mail'));
        } else {
            $this->stdHydrate($data, $object, array('become_member', 'bakske_by_mail'));

            $object->setTshirtSize(false)
                ->setReceiveIrreeelAtCudi(false);
        }

        return $this->stdHydrate($data, $object, self::$std_keys);
    }
}

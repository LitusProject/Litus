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

namespace SecretaryBundle\Hydrator\Organization;

use CommonBundle\Entity\User\Status\University as UniversityStatus,
    SecretaryBundle\Entity\Organization\MetaData as MetaDataEntity;

class MetaData extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array();

    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array();
        }

        /** @var \CommonBundle\Hydrator\User\Person\Academic $hydrator */
        $hydrator = $this->getHydrator('CommonBundle\Hydrator\User\Person\Academic');

        $data = array(
            'academic'          => $hydrator->extract($object->getAcademic()),
            'organization_info' => $this->stdExtract($object, self::$stdKeys),
        );

        $data['organization_info']['become_member'] = $object->becomeMember();

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

        /** @var \CommonBundle\Hydrator\User\Person\Academic $hydrator */
        $hydrator = $this->getHydrator('CommonBundle\Hydrator\User\Person\Academic');

        if (null === $object) {
            if (!isset($data['academic'])) {
                throw new LogicException('Cannot create a MetaData without Academic.');
            }

            $academic = $data['academic'];

            $academicEntity = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findOneByUniversityIdentification($academic['university']['identification']);

            $academic = $hydrator->hydrate($academic, $academicEntity);

            $object = new MetaDataEntity($academic, $year);
        } else {
            if (isset($data['academic'])) {
                $hydrator->hydrate($data['academic'], $object->getAcademic());
            }
        }

        $academic = $object->getAcademic();

        $data = $data['organization_info'];

        if ($data['become_member'] == "") {
            $data['become_member'] = false;
        }

        if ($academic->canHaveUniversityStatus($year)) {
            $academic->addUniversityStatus(
                new UniversityStatus(
                    $academic,
                    'student',
                    $year
                )
            );
        }

        $this->stdHydrate($data, $object, array('become_member'));

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }
}

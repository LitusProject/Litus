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

namespace ShiftBundle\Hydrator;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException,
    ShiftBundle\Entity\Shift as ShiftEntity;

class Shift extends CommonBundle\Component\Hydrator\Hydrator
{
    private static $std_keys = array(
        'nb_responsibles',
        'nb_volunteers',
        'name',
        'description',
        'reward',
        'handled_on_event',
    );

    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array();
        }

        $manager = $object->getManager();

        $data = $this->stdExtract($object, self::$std_keys);

        $data['manager_id'] = $manager->getId();
        $data['start_date'] = $object->getStartDate()->format('d/m/Y H:i');
        $data['end_date'] = $object->getEndDate()->format('d/m/Y H:i');
        $data['manager'] = $manager->getFullName()
                . ($manager instanceof Academic ? ' - ' . $manager->getUniversityIdentification() : '');
        $data['unit'] = $object->getUnit()->getId();
        $data['event'] = null === $object->getEvent()
                ? ''
                : $object->getEvent()->getId();
        $data['location'] = $object->getLocation()->getId();

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            $object = new ShiftEntity(
                $this->getPerson(),
                $this->getCurrentAcademicYear(true),
            );
        }

        if ($object->canEditDates()) {
            $object->setStartDate(self::loadDateTime($data['start_date']))
                ->setEndDate(self::loadDateTime($data['end_date']));
        }

        $peopleRepository = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person\Academic');

        $manager = ('' == $data['manager_id'])
            ? $peopleRepository->findOneByUsername($data['manager'])
            : $peopleRepository->findOneById($data['manager_id']);

        $editRoles = array();
        if (isset($data['edit_roles'])) {
            $roleRepository = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\Acl\Role');

            foreach ($data['edit_roles'] as $editRole) {
                $editRoles[] = $roleRepository->findOneByName($editRole);
            }
        }

        $object->setManager($manager)
            ->setUnit(
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Organization\Unit')
                    ->findOneById($data['unit'])
            )
            ->setLocation(
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Location')
                    ->findOneById($data['location'])
            )
            ->setEditRoles($editRoles);

        if ('' != $data['event']) {
            $object->setEvent(
                $this->getEntityManager()
                    ->getRepository('CalendarBundle\Entity\Node\Event')
                    ->findOneById($data['event'])
            );
        } else {
            $object->setEvent(null);
        }

        return $this->stdHydrate($data, $object, self::$std_keys);
    }
}

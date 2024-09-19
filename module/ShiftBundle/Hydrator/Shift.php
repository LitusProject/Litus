<?php

namespace ShiftBundle\Hydrator;

use CommonBundle\Entity\User\Person\Academic;
use ShiftBundle\Entity\Shift as ShiftEntity;

class Shift extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array(
        'nb_responsibles',
        'nb_volunteers',
        'nb_volunteers_min',
        'name',
        'description',
        'reward',
        'points',
        'handled_on_event',
        'ticket_needed',
    );

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $manager = $object->getManager();

        $data = $this->stdExtract($object, self::$stdKeys);

        $data['start_date'] = $object->getStartDate()->format('d/m/Y H:i');
        $data['end_date'] = $object->getEndDate()->format('d/m/Y H:i');
        $data['manager']['id'] = $manager->getId();
        $data['manager']['value'] = $manager->getFullName()
                . ($manager instanceof Academic ? ' - ' . $manager->getUniversityIdentification() : '');
        $data['unit'] = $object->getUnit()->getId();
        $data['event'] = $object->getEvent() === null ? '' : $object->getEvent()->getId();
        $data['location'] = $object->getLocation()->getId();
        $data['edit_roles'] = $this->createRolesPopulationArray($object->getEditRoles());

        return $data;
    }

    private function createRolesPopulationArray(array $roles)
    {
        $rolesArray = array();
        foreach ($roles as $role) {
            if ($role->getSystem()) {
                continue;
            }

            $rolesArray[] = $role->getName();
        }

        return $rolesArray;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new ShiftEntity(
                $this->getPersonEntity(),
                $this->getCurrentAcademicYear(true)
            );
        }

        if ($object->canEditDates()) {
            $object->setStartDate(self::loadDateTime($data['start_date']))
                ->setEndDate(self::loadDateTime($data['end_date']));
        }

        if ($data['manager']) {
            $manager = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findOneById($data['manager']['id']);
        } else {
            $manager = $this->getPersonEntity();
        }

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

        if ($data['event'] != '') {
            $object->setEvent(
                $this->getEntityManager()
                    ->getRepository('CalendarBundle\Entity\Node\Event')
                    ->findOneById($data['event'])
            );
        } else {
            $object->setEvent(null);
        }

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }
}

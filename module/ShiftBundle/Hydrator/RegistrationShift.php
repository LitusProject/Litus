<?php

namespace ShiftBundle\Hydrator;

use ShiftBundle\Entity\RegistrationShift as RegistrationShiftEntity;

class RegistrationShift extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array(
        'nb_registered',
        'members_only',
        'members_visible',
        'name',
        'description',
        'handled_on_event',
        'ticket_needed',
    );

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);

        $data['start_date'] = $object->getStartDate()->format('d/m/Y H:i');
        $data['end_date'] = $object->getEndDate()->format('d/m/Y H:i');
        $data['visible_date'] = $object->getVisibleDate() ? $object->getVisibleDate()->format('d/m/Y H:i') : '';
        $data['final_signin_date'] = $object->getFinalSigninDate() ? $object->getFinalSigninDate()->format('d/m/Y H:i') : '';
        $data['signout_date'] = $object->getSignoutDate() ? $object->getSignoutDate()->format('d/m/Y H:i') : '';
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
            $object = new RegistrationShiftEntity(
                $this->getPersonEntity(),
                $this->getCurrentAcademicYear(true)
            );
        }

        if ($object->canEditDates()) {
            $object->setStartDate(self::loadDateTime($data['start_date']))
                ->setEndDate(self::loadDateTime($data['end_date']))
                ->setVisibleDate(self::loadDateTime($data['visible_date']))
                ->setSignoutDate(self::loadDateTime($data['signout_date']))
                ->setFinalSigninDate(self::loadDateTime($data['final_signin_date']));
        }

        $editRoles = array();
        if (isset($data['edit_roles'])) {
            $roleRepository = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\Acl\Role');

            foreach ($data['edit_roles'] as $editRole) {
                $editRoles[] = $roleRepository->findOneByName($editRole);
            }
        }

        $object
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

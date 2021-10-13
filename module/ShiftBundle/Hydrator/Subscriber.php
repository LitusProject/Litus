<?php

namespace ShiftBundle\Hydrator;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;
use ShiftBundle\Entity\Shift\Responsible as ResponsibleEntity;
use ShiftBundle\Entity\Shift\Volunteer as VolunteerEntity;

class Subscriber extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected $entity = 'ShiftBundle\Entity\Shift';

    protected function doExtract($object = null)
    {
        return array();
    }

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            throw new InvalidObjectException();
        }

        $person = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person\Academic')
            ->findOneById($data['person']['id']);

        if ($data['responsible']) {
            if (!$object->canHaveAsResponsible($this->getEntityManager(), $person)) {
                return;
            }

            $subscriber = new ResponsibleEntity($person, $this->getCurrentAcademicYear());
            $object->addResponsible($this->getEntityManager(), $subscriber);
        } else {
            if (!$object->canHaveAsVolunteer($this->getEntityManager(), $person)) {
                return;
            }

            $subscriber = new VolunteerEntity($person);
            $object->addVolunteer($this->getEntityManager(), $subscriber);
        }

        return $subscriber;
    }
}

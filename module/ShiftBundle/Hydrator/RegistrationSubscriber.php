<?php

namespace ShiftBundle\Hydrator;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;
use ShiftBundle\Entity\Shift\Registered as RegistrationEntity;

class RegistrationSubscriber extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected $entity = 'ShiftBundle\Entity\RegistrationShift';

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

        if (!$object->canHaveAsRegistered($this->getEntityManager(), $person)) {
            return;
        }

        $subscriber = new RegistrationEntity($this->getCurrentAcademicYear(true), $person);
        $object->addRegistered($this->getEntityManager(), $subscriber);

        return $subscriber;
    }
}

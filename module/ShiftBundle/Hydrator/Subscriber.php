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
    ShiftBundle\Entity\Shift\Responsible as ResponsibleEntity,
    ShiftBundle\Entity\Shift\Volunteer as VolunteerEntity;

class Subscriber extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected $entity = 'ShiftBundle\Entity\Shift';

    private static $std_keys = array();

    protected function doExtract($object = null)
    {
        return array();
    }

    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
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

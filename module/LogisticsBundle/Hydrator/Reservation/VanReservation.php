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

namespace LogisticsBundle\Hydrator\Reservation;

use LogisticsBundle\Entity\Reservation\VanReservation as VanReservationEntity;

class VanReservation extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $std_keys = array('reason', 'load', 'additional_info');

    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array();
        }

        $data = $this->stdExtract($object, self::$std_keys);

        $data['driver'] = null !== $object->getDriver()
            ? $object->getDriver()->getPerson()->getId()
            : -1;

        if (null !== $object->getPassenger()) {
            $data['passenger']['id'] = $object->getPassenger()->getId();
            $data['passenger']['value'] = $object->getPassenger()->getFullName();
        }

        $data['start_date'] = $object->getStartDate()->format('d/m/Y H:i');
        $data['end_date'] = $object->getEndDate()->format('d/m/Y H:i');

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            $resource = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Reservation\ReservableResource')
                ->findOneByName(VanReservationEntity::VAN_RESOURCE_NAME);

            $object = new VanReservationEntity($resource, $this->getPerson());
        }

        $driver = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Driver')
            ->findOneById($data['driver']);

        $passenger = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person\Academic')
            ->findOneById($data['passenger']['id']);

        if (null !== $driver) {
            $object->setDriver($driver);
        }

        if (null !== $passenger) {
            $object->setPassenger($passenger);
        }

        if (isset($data['start_date'])) {
            $object->setStartDate(self::loadDateTime($data['start_date']));
        }

        if (isset($data['end_date'])) {
            $object->setEndDate(self::loadDateTime($data['end_date']));
        }

        return $this->stdHydrate($data, $object, self::$std_keys);
    }
}

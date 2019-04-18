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

namespace LogisticsBundle\Hydrator\Reservation;

use LogisticsBundle\Entity\Reservation\Van as VanReservationEntity;

class Van extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('reason', 'load', 'additional_info', 'car', 'bike');

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);

        $data['driver'] = $object->getDriver() !== null ? $object->getDriver()->getPerson()->getId() : -1;

        if ($object->getPassenger() !== null) {
            $data['passenger']['id'] = $object->getPassenger()->getId();
            $data['passenger']['value'] = $object->getPassenger()->getFullName();
        }

        $data['start_date'] = $object->getStartDate()->format('d/m/Y H:i');
        $data['end_date'] = $object->getEndDate()->format('d/m/Y H:i');

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $resource = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Reservation\Resource')
                ->findOneByName(VanReservationEntity::RESOURCE_NAME);

            $object = new VanReservationEntity($resource, $this->getPersonEntity());
        }

        $driver = null;
        if (array_key_exists('driver', $data)) {
            $driver = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Driver')
                ->findOneById($data['driver']);
        }

        if ($data['passenger']['id'] != '') {
            $passenger = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findOneById($data['passenger']['id']);

            if ($passenger !== null) {
                $object->setPassenger($passenger);
            }
        }

        if ($driver !== null) {
            $object->setDriver($driver);
        }

        if (isset($data['start_date'])) {
            $object->setStartDate(self::loadDateTime($data['start_date']));
        }

        if (isset($data['end_date'])) {
            $object->setEndDate(self::loadDateTime($data['end_date']));
        }

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }
}

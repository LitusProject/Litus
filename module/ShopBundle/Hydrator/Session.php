<?php

namespace ShopBundle\Hydrator;

use ShopBundle\Entity\Session as SessionEntity;

class Session extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('reservations_possible', 'remarks');

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);
        $data['start_date'] = $object->getStartDate()->format('d/m/Y H:i');
        $data['end_date'] = $object->getEndDate()->format('d/m/Y H:i');
        $data['final_reservation_date'] = $object->getFinalReservationDate() ? $object->getFinalReservationDate()->format('d/m/Y H:i') : ' ';
        $data['rewards_amount'] = $object->getAmountRewards() ?? 0;

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new SessionEntity();
        }
        $object = $this->stdHydrate($data, $object, self::$stdKeys);
        $object->setStartDate(self::loadDateTime($data['start_date']));
        $object->setEndDate(self::loadDateTime($data['end_date']));
        $object->setFinalReservationDate(self::loadDateTime($data['final_reservation_date']));
        $object->setAmountRewards($data['rewards_amount']);

        return $object;
    }
}

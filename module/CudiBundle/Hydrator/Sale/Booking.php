<?php

namespace CudiBundle\Hydrator\Sale;

use CudiBundle\Entity\Sale\Booking as BookingEntity;

class Booking extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected function doExtract($object = null)
    {
        return array();
    }

    protected function doHydrate(array $data, $object = null)
    {
        return new BookingEntity(
            $this->getEntityManager(),
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findOneById($data['person']['id']),
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Article')
                ->findOneById($data['article']['id']),
            'booked',
            $data['amount'],
            true
        );
    }
}

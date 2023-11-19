<?php

namespace LogisticsBundle\Hydrator\Order;

use LogisticsBundle\Entity\Order\OrderArticleMap as MapEntity;

class OrderArticleMap extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('amount');

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new MapEntity(
                $this->getEntityManager()
                    ->getRepository('LogisticsBundle\Entity\Order')
                    ->findOneById($data['order']['id']),
                $this->getEntityManager()
                    ->getRepository('LogisticsBundle\Entity\Article')
                    ->findOneById($data['article']['id']),
                $data['amount'],
            );
        }

        $object->setStatus($data['status']);

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);
        $data['status'] = $object->getStatusKey();

        return $data;
    }
}

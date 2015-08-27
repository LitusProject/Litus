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

namespace ShopBundle\Hydrator;

use DateTime,
    ShopBundle\Entity\Reservation as ReservationEntity;

class Reservation extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array(
        'amount',
    );

    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            $object = new ReservationEntity();
        }

        $object = $this->stdHydrate($data, $object, self::$stdKeys);

        $salesSession = $this->getEntityManager()
            ->getRepository('ShopBundle\Entity\SalesSession')
            ->find($data['salesSession']);
        $object->setSalesSession($salesSession);

        $product = $this->getEntityManager()
            ->getRepository('ShopBundle\Entity\Product')
            ->find($data['product']);
        $object->setProduct($product);

        $object->setTimestamp(new DateTime());

        $object->setNoShow(false);

        return $object;
    }
}

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

use ShopBundle\Entity\ReservationPermission as ReservationPermissionEntity;

/**
 * Class ReservationPermission
 * @author Floris Kint <floris.kint@litus.cc>
 */
class ReservationPermission extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array(
        'reservationsAllowed',
    );

    protected function doExtract($object = null)
    {
        return array();
    }

    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            $object = new ReservationPermissionEntity();
        }

        $object = $this->stdHydrate($data, $object, self::$stdKeys);

        $person = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person')
            ->find($data['person']['id']);
        $object->setPerson($person);

        return $object;
    }
}

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

use ShopBundle\Entity\SalesSession as SalesSessionEntity;

/**
 * Class SalesSession
 * @author Floris Kint <floris.kint@litus.cc>
 */
class SalesSession extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array(
        'reservations_possible',
        'remarks',
    );

    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);
        $data['start_date'] = $object->getStartDate()->format('d/m/Y H:i');
        $data['end_date'] = $object->getEndDate()->format('d/m/Y H:i');

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            $object = new SalesSessionEntity();
        }

        $object = $this->stdHydrate($data, $object, self::$stdKeys);
        $object->setStartDate(self::loadDateTime($data['start_date']));
        $object->setEndDate(self::loadDateTime($data['end_date']));

        return $object;
    }
}

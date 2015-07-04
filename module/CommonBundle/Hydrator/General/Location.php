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

namespace CommonBundle\Hydrator\General;

use CommonBundle\Entity\General\Location as LocationEntity;

class Location extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $geoKeys = array(
        'latitude', 'longitude',
    );

    private static $stdKeys = array('name');

    protected function doExtract($object = null)
    {
        /** @var \CommonBundle\Hydrator\General\Address $hydrator */
        $hydrator = $this->getHydrator('CommonBundle\Hydrator\General\Address');

        if (null === $object) {
            return array(
                'address' => $hydrator->extract(null),
            );
        }

        $data = $this->stdExtract($object, array('name'));

        $data['geographical'] = $this->stdExtract($object, self::$geoKeys);

        $data['address'] = $hydrator->extract($object->getAddress());

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            $object = new LocationEntity();
        }

        /** @var \CommonBundle\Hydrator\General\Address $hydrator */
        $hydrator = $this->getHydrator('CommonBundle\Hydrator\General\Address');

        if (isset($data['address'])) {
            $object->setAddress(
                $hydrator->hydrate($data['address'], $object->getAddress())
            );
        }

        if (isset($data['geographical'])) {
            $this->stdHydrate($data['geographical'], $object, self::$geoKeys);
        }

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }
}

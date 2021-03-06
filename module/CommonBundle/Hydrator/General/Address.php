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

namespace CommonBundle\Hydrator\General;

use CommonBundle\Entity\General\Address as AddressEntity;

class Address extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array(
        'street', 'number', 'mailbox',
        'postal', 'city',
    );

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array(
                'country' => 'BE',
            );
        }

        $data = $this->stdExtract($object, self::$stdKeys);
        $data['country'] = $object->getCountryCode();

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new AddressEntity();
        }

        return $this->stdHydrate($data, $object, array(self::$stdKeys, 'country'));
    }
}

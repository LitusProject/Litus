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

use CommonBundle\Entity\General\Address as AddressEntity;

class Address extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $std_keys = array(
        'street', 'number', 'mailbox',
        'postal', 'city',
    );

    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array(
                'country' => 'BE',
            );
        }

        $data = $this->stdExtract($object, self::$std_keys);
        $data['country'] = $object->getCountryCode();

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            $object = new AddressEntity();
        }

        return $this->stdHydrate($data, $object, array(self::$std_keys, 'country'));
    }
}

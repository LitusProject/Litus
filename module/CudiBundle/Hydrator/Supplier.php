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

namespace CudiBundle\Hydrator;

use CudiBundle\Entity\Supplier as SupplierEntity;

class Supplier extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $std_keys = array(
        'name', 'phone_number', 'vat_number', 'template', 'contact',
    );

    protected function doExtract($object = null)
    {
        $addressHydrator = $this->getHydrator('CommonBundle\Hydrator\General\Address');

        if (null === $object) {
            return array(
                'address' => $addressHydrator->extract(null),
            );
        }

        $data = $this->stdExtract($object, self::$std_keys);
        $data['address'] = $addressHydrator->extract($object->getAddress());

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            $object = new SupplierEntity();
        }

        $object->setAddress(
            $this->getHydrator('CommonBundle\Hydrator\General\Address')
                ->hydrate($data['address'], $object->getAddress())
        );

        return $this->stdHydrate($data, $object, self::$std_keys);
    }
}

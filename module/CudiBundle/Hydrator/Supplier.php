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

namespace CudiBundle\Hydrator;

use CudiBundle\Entity\Supplier as SupplierEntity;

class Supplier extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array(
        'name', 'phone_number', 'vat_number', 'template', 'contact',
    );

    protected function doExtract($object = null)
    {
        /** @var \CommonBundle\Hydrator\General\Address $hydratorAddress */
        $hydratorAddress = $this->getHydrator('CommonBundle\Hydrator\General\Address');

        if (null === $object) {
            return array(
                'address' => $hydratorAddress->extract(null),
            );
        }

        $data = $this->stdExtract($object, self::$stdKeys);
        $data['address'] = $hydratorAddress->extract($object->getAddress());

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            $object = new SupplierEntity();
        }

        /** @var \CommonBundle\Hydrator\General\Address $hydratorAddress */
        $hydratorAddress = $this->getHydrator('CommonBundle\Hydrator\General\Address');

        $object->setAddress(
            $hydratorAddress->hydrate($data['address'], $object->getAddress())
        );

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }
}

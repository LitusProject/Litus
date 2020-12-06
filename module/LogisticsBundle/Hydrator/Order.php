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

namespace LogisticsBundle\Hydrator;

use CommonBundle\Entity\General\Organization\Unit;
use CommonBundle\Entity\User\Person\Academic;
use LogisticsBundle\Entity\Order as OrderEntity;


use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;

/**
 * This hydrator hydrates/extracts Order data.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Matthias Swiggers <matthias.swiggers@vtk.be>
 */
class Order extends \CommonBundle\Component\Hydrator\Hydrator
{
    /**
     * @static @var string[] Key attributes to hydrate using the standard method.
     */
    private static $stdKeys = array('name', 'description', 'email', 'contact');

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new OrderEntity($data['contact']);
        }

        $creator = $this->getPersonEntity();
        $object->setCreator($creator);
        $object->setLocation(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Location')
                ->findOneById($data['location'])
        );
        $object->setUnit(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Organization\Unit')
                ->findOneById($data['unit'])
        );

        $object->updateDate();

        $object->setStartDate(self::loadDateTime($data['start_date']))
            ->setEndDate(self::loadDateTime($data['end_date']));

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }
        $unit = $object->getUnit();

        $data = $this->stdExtract($object, self::$stdKeys);

        $data['location'] = $object->getLocation()->getId();
        $data['unit'] = $unit->getId();
        $data['start_date'] = $object->getStartDate()->format('d/m/Y H:i');
        $data['end_date'] = $object->getEndDate()->format('d/m/Y H:i');

        return $data;
    }
}

<?php

namespace LogisticsBundle\Hydrator;

use CommonBundle\Entity\General\Organization\Unit;
use Doctrine\Common\Collections\ArrayCollection;
use LogisticsBundle\Entity\Order as OrderEntity;

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
    private static array $stdKeys = array('name', 'description', 'location', 'transport');

    protected function doHydrate(array $data, $object = null): object
    {
        if ($object === null) {
            $object = new OrderEntity($this->getPersonEntity());
        }

        $units = new arrayCollection();
        foreach ($data['units'] as $unitId) {
            $units->add(
                $this->getEntityManager()
                    ->getRepository(Unit::class)
                    ->findOneById($unitId)
            );
        }
        $object->setUnits($units);

        $object->setStartDate(self::loadDateTime($data['start_date']))
            ->setEndDate(self::loadDateTime($data['end_date']));

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }

    protected function doExtract($object = null): array
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);

        $data['unit'] = array();
        foreach ($object->getUnits() as $unit) {
            $data['unit'][] = $unit->getId();
        }
        $data['name'] = $object->getName();
        $data['start_date'] = $object->getStartDate()->format('d/m/Y H:i');
        $data['end_date'] = $object->getEndDate()->format('d/m/Y H:i');
        $data['status'] = $object->getStatus();

        return $data;
    }
}

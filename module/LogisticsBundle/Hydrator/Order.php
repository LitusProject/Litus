<?php

namespace LogisticsBundle\Hydrator;

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
    private static $stdKeys = array('description', 'email', 'contact', 'needs_ride', 'internal_comment', 'external_comment');

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new OrderEntity($data['contact'], null, null);
        }

        $object->setContact($data['contact']);
        $creator = $this->getPersonEntity();
        $object->setCreator($creator);
        $object->setLocation(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Location')
                ->findOneById($data['location'])
        );

        $units = new ArrayCollection();
        foreach ($data['unit'] as $unitId) {
            $units->add(
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Organization\Unit')
                    ->findOneById($unitId)
            );
        }
        $object->setUnits($units);

        if (isset($data['name']) && $data['name'] !== null) {
            $object->setName($data['name']);
        }

        $object->updateDate();
        $object->setStartDate(self::loadDateTime($data['start_date']))
            ->setEndDate(self::loadDateTime($data['end_date']));

        if (isset($data['status'])) {
            if ($data['status'] !== null) {
                $object->setStatus($data['status']);
            }
        }


        return $this->stdHydrate($data, $object, self::$stdKeys);
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);

        $data['location'] = $object->getLocation()->getId();
        $data['unit'] = array();
        foreach ($object->getUnits() as $unit) {
            $data['unit'][] = $unit->getId();
        }
        $data['name'] = $object->getName();
        $data['start_date'] = $object->getStartDate()->format('d/m/Y H:i');
        $data['end_date'] = $object->getEndDate()->format('d/m/Y H:i');
        $data['status'] = strtolower($object->getStatus());

        return $data;
    }
}

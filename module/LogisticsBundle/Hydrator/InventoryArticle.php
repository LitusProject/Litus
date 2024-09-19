<?php

namespace LogisticsBundle\Hydrator;

use CommonBundle\Entity\General\Organization\Unit;
use LogisticsBundle\Entity\InventoryArticle as InventoryArticleEntity;
use LogisticsBundle\Entity\InventoryCategory;

/**
 * This hydrator hydrates/extracts InventoryArticle data.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Matthias Swiggers <matthias.swiggers@vtk.be>
 * @author Pedro Devogelaere <pedro.devogelaere@vtk.be>
 */
class InventoryArticle extends \CommonBundle\Component\Hydrator\Hydrator
{
    /**
     * @static @var string[] Key attributes to hydrate using the standard method.
     */
    private static array $stdKeys = array('name', 'amount', 'location', 'spot', 'visibility', 'status', 'deposit', 'rent', 'external_comment', 'internal_comment');

    protected function doHydrate(array $data, $object = null): object
    {
        if ($object === null) {
            $object = new InventoryArticleEntity();
        }

        if ($data['category']) {
            $object->setCategory(
                $this->getEntityManager()
                    ->getRepository(InventoryCategory::class)
                    ->findOneById($data['category'])
            );
        }

        $object->setUnit(
            $this->getEntityManager()
                ->getRepository(Unit::class)
                ->findOneById($data['unit'])
        );

        if ($data['warranty_date']) {
            $object->setWarrantyDate(self::loadDateTime($data['warranty_date']));
        }

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }

    protected function doExtract($object = null): array
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);

        $data['unit'] = $object->getUnit()->getId();
        if ($object->getCategory()) {
            $data['category'] = $object->getCategory()->getId();
        }
        if ($object->getWarrantyDate()) {
            $data['warranty_date'] = $object->getWarrantyDate()->format('d/m/Y H:i');
        }

        return $data;
    }
}

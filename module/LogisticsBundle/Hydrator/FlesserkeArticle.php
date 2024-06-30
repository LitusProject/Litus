<?php

namespace LogisticsBundle\Hydrator;

use LogisticsBundle\Entity\FlesserkeArticle as FlesserkeArticleEntity;
use LogisticsBundle\Entity\FlesserkeCategory;

/**
 * This hydrator hydrates/extracts FlesserkeArticle data.
 *
 * @author Pedro Devogelaere <pedro.devogelaere@vtk.be>
 */
class FlesserkeArticle extends \CommonBundle\Component\Hydrator\Hydrator
{
    /**
     * @static @var string[] Key attributes to hydrate using the standard method.
     */
    private static array $stdKeys = array('barcode', 'name', 'amount', 'brand', 'unit', 'per_unit', 'internal_comment');

    protected function doHydrate(array $data, $object = null): object
    {
        if ($object === null) {
            $object = new FlesserkeArticleEntity();
        }

        if ($data['category']) {
            $object->setCategory(
                $this->getEntityManager()
                    ->getRepository(FlesserkeCategory::class)
                    ->findOneById($data['category'])
            );
        }

        if ($data['expiration_date']) {
            $object->setExpirationDate(self::loadDateTime($data['expiration_date']));
        }

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }

    protected function doExtract($object = null): array
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);

        if ($object->getCategory()) {
            $data['category'] = $object->getCategory()->getId();
        }
        if ($object->getExpirationDate()) {
            $data['expiration_date'] = $object->getExpirationDate()->format('d/m/Y H:i');
        }

        return $data;
    }
}

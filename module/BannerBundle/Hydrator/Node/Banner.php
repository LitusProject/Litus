<?php

namespace BannerBundle\Hydrator\Node;

use BannerBundle\Entity\Node\Banner as BannerEntity;
use CommonBundle\Component\Hydrator\Exception\InvalidDateException;

/**
 * This hydrator hydrates/extracts Banner data.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Banner extends \CommonBundle\Component\Hydrator\Hydrator
{
    /**
     * @static @var string[] Key attributes to hydrate using the standard method.
     */
    private static $stdKeys = array('name', 'active', 'url');

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new BannerEntity($this->getPersonEntity());
        }

        $startDate = self::loadDateTime($data['start_date']);
        $endDate = self::loadDateTime($data['end_date']);

        if ($startDate === null || $endDate === null) {
            throw new InvalidDateException();
        }

        $object->setStartDate($startDate)
            ->setEndDate($endDate);

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);

        $data['start_date'] = $object->getStartDate()->format('d/m/Y H:i');
        $data['end_date'] = $object->getEndDate()->format('d/m/Y H:i');
        $data['active'] = $object->isActive();

        return $data;
    }
}

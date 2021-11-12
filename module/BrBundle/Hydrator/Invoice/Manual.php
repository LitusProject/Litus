<?php

namespace BrBundle\Hydrator\Invoice;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;

/**
 * This hydrator hydrates/extracts Manual Invoice data.
 *
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 */
class Manual extends \CommonBundle\Component\Hydrator\Hydrator
{
    /**
     * @static @var string[] Key attributes to hydrate using the standard method.
     */
    private static $stdKeys = array('title', 'price', 'payment_days', 'refund');

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            throw new InvalidObjectException('Cannot create a manual invoice');
        }

        $company = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findOneById($data['company']);

        $object->setCompany($company);

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);

        $data['company'] = $object->getCompany()->getId();

        return $data;
    }
}

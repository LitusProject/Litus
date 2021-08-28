<?php

namespace BrBundle\Hydrator;

use BrBundle\Entity\Product as ProductEntity;
use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;

/**
 * This hydrator hydrates/extracts Product data.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Product extends \CommonBundle\Component\Hydrator\Hydrator
{
    /**
     * @static @var string[] Key attributes to hydrate using the standard method.
     */
    private static $stdKeys = array('name', 'description', 'invoice_description', 'contract_text_nl', 'contract_text_en', 'price', 'vat_type', 'refund');

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            throw new InvalidObjectException('Cannot create a product');
        }

        if ($object->getName() != null) {
            $orderEntry = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Product\Order\Entry')
                ->findOneByProduct($object->getId());

            if ($orderEntry !== null) {
                $object->setOld();

                $object = new ProductEntity(
                    $object->getAuthor(),
                    $object->getAcademicYear()
                );
            }
        }

        if ($data['delivery_date'] != '') {
            $object->setDeliveryDate(self::loadDate($data['delivery_date']));
        }

        if ($data['event'] != '') {
            $object->setEvent(
                $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\Event')
                    ->findOneById($data['event'])
            );
        }

        $this->getEntityManager()->persist($object);

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }

    protected function doExtract($object = null)
    {

        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);
        if ($object->getDeliveryDate() !== null) {
            $data['delivery_date'] = $object->getDeliveryDate()->format('d/m/Y');
        }
        if ($object->getEvent() !== null) {
            $data['event'] = $object->getEvent()->getId() ?? null;
        }

        return $data;
    }
}

<?php

namespace BrBundle\Hydrator\Product;

use BrBundle\Entity\Product\Order as OrderEntity;
use BrBundle\Entity\Product\Order\Entry as OrderEntryEntity;
use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;

/**
 * This hydrator hydrates/extracts Order data.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Order extends \CommonBundle\Component\Hydrator\Hydrator
{
    /**
     * @static @var string[] Key attributes to hydrate using the standard method.
     */
    private static $stdKeys = array('discount', 'discount_context');

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            throw new InvalidObjectException('Cannot create an order');
        }

        if ($object->getContact() !== null && $object->hasContract()) {
            $object->setOld();

            $company = $object->getContract()->getCompany();
            $entries = $object->getEntries();

            $object = new OrderEntity(
                $object->getCreationPerson()
            );
        } else {
            $company = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Company')
                ->findOneById($data['company']);
        }

        $object->setContact(
            $this->getEntityManager()
                ->getRepository('BrBundle\Entity\User\Person\Corporate')
                ->findOneById($data['contact_' . $company->getId()])
        );

        if (isset($entries)) {
            foreach ($entries as $entry) {
                $orderEntry = new OrderEntryEntity($object, $entry->getProduct(), $entry->getQuantity());
                $object->setEntry($orderEntry);

                $this->getEntityManager()->persist($orderEntry);
            }
        }

        if (isset($data['new_product'])) {
            $product = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Product')
                ->findProductByIdQuery($data['new_product'])
                ->getResult()[0];

            $orderEntry = new OrderEntryEntity($object, $product, $data['new_product_amount']);
            $object->setEntry($orderEntry);
        }

        $object->setEntityManager($this->getEntityManager());
        $object->setAutoDiscount($data['auto_discount']);
        $object->setAutoDiscountPercentage();

        $this->getEntityManager()->persist($object);
        $this->getEntityManager()->flush();

        if (isset($data['edit_product'])) {
            $product = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Product')
                ->findProductByIdQuery($data['edit_product'])
                ->getResult()[0];

            $entry = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Product\Order\Entry')
                ->findOneByOrderAndProduct($object, $product);

            $entry->setQuantity($data['edit_product_amount']);
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

        $data['auto_discount'] = $object->hasAutoDiscount();
        $data['company'] = $object->getCompany()->getId();
        $data['contact_' . $object->getCompany()->getId()] = $object->getContact()->getId();

        return $data;
    }
}

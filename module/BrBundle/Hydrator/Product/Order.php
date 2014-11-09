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
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Hydrator\Product;

use BrBundle\Entity\Contract as ContractEntity,
    BrBundle\Entity\Contract\ContractEntry as ContractEntryEntity,
    BrBundle\Entity\Contract\ContractHistory as ContractHistoryEntity,
    BrBundle\Entity\Product\Order as OrderEntity,
    BrBundle\Entity\Product\OrderEntry as OrderEntryEntity,
    CommonBundle\Component\Hydrator\Exception\InvalidObjectException;

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
    private static $std_keys = array('tax_free');

    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            throw new InvalidObjectException('Cannot create a contract');
        }

        if (null !== $object->getContact()) {
            $object->setOld();

            $company = $object->getContract()->getCompany();

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

        $contract = new ContractEntity(
            $object,
            $object->getCreationPerson(),
            $company,
            $data['discount'],
            $data['title']
        );

        $contract->setContractNb(
            $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Contract')
                ->findNextContractNb()
        );

        $contract->setDiscountContext($data['discount_context']);
        $object->setContract($contract);

        $products = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Product')
            ->findByAcademicYear($this->getCurrentAcademicYear());

        $counter = 0;
        foreach ($products as $product) {
            if ($data['product_' . $product->getId()] > 0) {
                $orderEntry = new OrderEntryEntity($object, $product, $data['product_' . $product->getId()]);
                $contractEntry = new ContractEntryEntity($contract, $orderEntry, $counter, 0);
                $counter++;
                $object->setEntry($orderEntry);
                $contract->setEntry($contractEntry);
            }
        }

        if (isset($data['new_product'])) {
            $products = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Product')
                ->findByAcademicYear($data['new_product']);

            $orderEntry = new OrderEntryEntity($object, $product, $data['new_product_amount']);
            $contractEntry = new ContractEntryEntity($contract, $orderEntry, $counter, 0);
            $object->setEntry($orderEntry);
            $contract->setEntry($contractEntry);
        }

        $this->getEntityManager()->persist($object);

        $this->getEntityManager()->persist(new ContractHistoryEntity($contract));

        return $this->stdHydrate($data, $object, self::$std_keys);
    }

    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array();
        }

        $data = $this->stdExtract($object, self::$std_keys);

        $data['title'] = $object->getContract()->getTitle();
        $data['company'] = $object->getCompany()->getId();
        $data['contact_' . $object->getCompany()->getId()] = $object->getContact()->getId();
        $data['discount'] = $object->getContract()->getDiscount();
        $data['discount_context'] = $object->getContract()->getDiscountContext();

        foreach ($object->getEntries() as $entry) {
            $data['product_' . $entry->getProduct()->getId()] = $entry->getQuantity();
        }

        return $data;
    }
}

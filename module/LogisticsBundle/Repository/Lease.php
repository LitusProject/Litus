<?php

namespace LogisticsBundle\Repository;

use LogisticsBundle\Entity\Lease\Item as ItemEntity;

/**
 * Lease
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Lease extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    /**
     * Finds all leases of an item that have not yet been returned
     *
     * @param  ItemEntity $item
     * @return \Doctrine\ORM\Query
     */
    public function findUnreturnedByItemQuery(ItemEntity $item)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('l')
            ->from('LogisticsBundle\Entity\Lease', 'l')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('l.returned', 'false'),
                    $query->expr()->eq('l.item', ':item')
                )
            )
            ->setParameter('item', $item)
            ->getQuery();
    }

    /**
     * Finds all leases that have not yet been returned
     *
     * @return \Doctrine\ORM\Query
     */
    public function findAllUnreturnedQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('l')
            ->from('LogisticsBundle\Entity\Lease', 'l')
            ->where(
                $query->expr()->eq('l.returned', 'false')
            )
            ->getQuery();
    }

    public function findByItemQuery(ItemEntity $item)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('l')
            ->from('LogisticsBundle\Entity\Lease', 'l')
            ->where(
                $query->expr()->eq('l.item', ':item')
            )
            ->setParameter('item', $item)
            ->getQuery();
    }
}

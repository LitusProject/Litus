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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace ShopBundle\Repository;

use CommonBundle\Component\Doctrine\ORM\EntityRepository,
    Doctrine\ORM\Query\Expr;

/**
 * Product
 * @author Floris Kint <floris.kint@litus.cc>
 */
class Product extends EntityRepository
{
    /**
     * @return \Doctrine\ORM\Query
     */
    public function findAllQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('p')
            ->from('ShopBundle\Entity\Product', 'p')
            ->orderBy('p.name', 'ASC')
            ->getQuery();

        return $resultSet;
    }

    /**
     * @param  string              $name
     * @return \Doctrine\ORM\Query
     */
    public function findAllByNameQuery($name)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('p')
            ->from('ShopBundle\Entity\Product', 'p')
            ->where(
                $query->expr()->like($query->expr()->lower('p.name'), ':name')
            )
            ->orderBy('p.name', 'ASC')
            ->setParameter('name', '%' . strtolower($name) . '%')
            ->getQuery();

        return $resultSet;
    }

    /**
     * @return array
     */
    public function findAllAvailable()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('p')
            ->from('ShopBundle\Entity\Product', 'p')
            ->where(
                $query->expr()->eq('p.available', ':available')
            )
            ->orderBy('p.name', 'ASC')
            ->setParameter('available', true)
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    /**
     * @param  SalesSession $salesSession
     * @return Product[]
     */
    public function findAvailableAndStockAndReservation($salesSession)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $stockSubQueryBuilder = $this->getEntityManager()->createQueryBuilder();
        $goodProducts = $stockSubQueryBuilder->select('p2')
            ->from('ShopBundle\Entity\Product\SessionStockEntry', 'sse')
            ->join('ShopBundle\Entity\Product', 'p2', Expr\Join::WITH, 'p2.id = sse.product')
            ->where(
                $stockSubQueryBuilder->expr()->eq('sse.salesSession', ':session')
            )
            ->setParameter('session', $salesSession)
            ->getQuery()
            ->getResult();
        $goodProductIds = array();
        foreach ($goodProducts as $product) {
            $goodProductIds[] = $product->getId();
        }
        $reservationsSubQueryBuilder = $this->getEntityManager()->createQueryBuilder();
        $goodProducts = $reservationsSubQueryBuilder->select('p2')
            ->from('ShopBundle\Entity\Reservation', 'r')
            ->join('ShopBundle\Entity\Product', 'p2', Expr\Join::WITH, 'p2.id = r.product')
            ->where(
                $reservationsSubQueryBuilder->expr()->eq('r.salesSession', ':session')
            )
            ->setParameter('session', $salesSession)
            ->getQuery()
            ->getResult();
        foreach ($goodProducts as $product) {
            $goodProductIds[] = $product->getId();
        }
        $resultSet = $query->select('p')
            ->from('ShopBundle\Entity\Product', 'p')
            ->where(
                $query->expr()->orX(
                    $query->expr()->in('p.id', $goodProductIds),
                    $query->expr()->eq('p.available', ':available')
                )
            )
            ->orderBy('p.name', 'ASC')
            ->setParameter('available', true)
            ->getQuery()
            ->getResult();

        return $resultSet;
    }
}

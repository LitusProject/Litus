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

namespace ShopBundle\Repository\Product;

use CommonBundle\Component\Doctrine\ORM\EntityRepository;

/**
 * Product
 * @author Floris Kint <floris.kint@litus.cc>
 */
class SessionStockEntry extends EntityRepository
{
    /**
	 * @param $product
	 * @return integer
	 */
    public function getProductAvailability($product, $salesSession)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('e')
            ->from('ShopBundle\Entity\Product\SessionStockEntry', 'e')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('e.product', ':product'),
                    $query->expr()->eq('e.salesSession', ':session')
                )
            )
            ->setParameter('product', $product)
            ->setParameter('session', $salesSession)
            ->getQuery()
            ->getOneOrNullResult();

        return $resultSet ? $resultSet->getAmount() : 0;
    }

    /**
	 * @param $product
	 * @param $salesSession
	 * @return integer
	 */
    public function getProductReservationsAmount($product, $salesSession)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('SUM(r.amount) as total')
            ->from('ShopBundle\Entity\Reservation', 'r')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('r.product', ':product'),
                    $query->expr()->eq('r.salesSession', ':session')
                )
            )
            ->setParameter('product', $product)
            ->setParameter('session', $salesSession)
            ->getQuery()
            ->getOneOrNullResult();

        return $resultSet ? $resultSet['total'] : 0;
    }

    /**
	 * @param SalesSession $salesSession
	 */
    public function deleteStockEntries($salesSession)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->delete('ShopBundle\Entity\Product\SessionStockEntry', 's')
            ->where($query->expr()->eq('s.salesSession', ':session'))
            ->setParameter('session', $salesSession)
            ->getQuery()
            ->execute();
    }

    /**
	 * @param Product $product
	 * @param SalesSession $salesSession
	 * @return int
	 */
    public function getRealAvailability($product, $salesSession)
    {
        return $this->getProductAvailability($product, $salesSession) - $this->getProductReservationsAmount($product, $salesSession);
    }
}

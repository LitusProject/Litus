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

namespace ShopBundle\Repository;

use CommonBundle\Component\Doctrine\ORM\EntityRepository,
    DateTime;

/**
 * SalesSession
 */
class SalesSession extends EntityRepository
{
    /**
	 * @return \Doctrine\ORM\Query
	 */
    public function findAllFutureQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('s')
            ->from('ShopBundle\Entity\SalesSession', 's')
            ->where(
                $query->expr()->gte('s.startDate', ':now')
            )
            ->orderBy('s.startDate', 'ASC')
            ->setParameter('now', new DateTime())
            ->getQuery();

        return $resultSet;
    }

    /**
	 * @return \Doctrine\ORM\Query
	 */
    public function findAllOldQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('s')
            ->from('ShopBundle\Entity\SalesSession', 's')
            ->where(
                $query->expr()->lt('s.startDate', ':now')
            )
            ->orderBy('s.startDate', 'ASC')
            ->setParameter('now', new DateTime())
            ->getQuery();

        return $resultSet;
    }

    /**
	 * @param  string $remarks
	 * @return \Doctrine\ORM\Query
	 */
    public function findAllFutureByRemarksQuery($remarks)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('s')
            ->from('ShopBundle\Entity\SalesSession', 's')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('s.remarks'), ':remarks'),
                    $query->expr()->gte('s.startDate', ':now')
                )
            )
            ->orderBy('s.startDate', 'ASC')
            ->setParameter('remarks', '%' . strtolower($remarks) . '%')
            ->setParameter('now', new DateTime())
            ->getQuery();

        return $resultSet;
    }

    /**
	 * @param  string $remarks
	 * @return \Doctrine\ORM\Query
	 */
    public function findAllOldByRemarksQuery($remarks)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('s')
            ->from('ShopBundle\Entity\SalesSession', 's')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('s.remarks'), ':remarks'),
                    $query->expr()->lt('s.startDate', ':now')
                )
            )
            ->orderBy('s.startDate', 'ASC')
            ->setParameter('remarks', '%' . strtolower($remarks) . '%')
            ->setParameter('now', new DateTime())
            ->getQuery();

        return $resultSet;
    }

    public function findAllReservationsPossibleInterval($startDate, $endDate)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('s')
            ->from('ShopBundle\Entity\SalesSession', 's')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('s.reservationsPossible', ':reservations_possible'),
                    $query->expr()->lt('s.startDate', ':end_date'),
                    $query->expr()->gt('s.startDate', ':start_date')
                )
            )
            ->orderBy('s.startDate', 'ASC')
            ->setParameter('reservations_possible', true)
            ->setParameter('start_date', $startDate)
            ->setParameter('end_date', $endDate)
            ->getQuery()
            ->getResult();

        return $resultSet;
    }
}

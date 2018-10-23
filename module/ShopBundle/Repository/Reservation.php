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
    DateTime,
    Doctrine\ORM\Query\ResultSetMapping;

/**
 * Reservation
 * @author Floris Kint <floris.kint@litus.cc>
 */
class Reservation extends EntityRepository
{
    /**
     * @param $person
     * @return array
     */
    public function getAllCurrentReservationsByPerson($person)
    {
        $query = $this->getEntityManager()->createQueryBuilder();

        return $query->select('r')
            ->from('ShopBundle\Entity\Reservation', 'r')
            ->join('r.salesSession', 'ss')
            ->where(
                $query->expr()->andX(
                    $query->expr()->gt('ss.endDate', ':now'),
                    $query->expr()->eq('r.person', ':person')
                )
            )
            ->orderBy('ss.startDate', 'ASC')
            ->setParameter('now', new DateTime())
            ->setParameter('person', $person)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $salesSession
     * @return \Doctrine\ORM\Query
     */
    public function findBySalesSessionQuery($salesSession)
    {
        $query = $this->getEntityManager()->createQueryBuilder();

        return $query->select('r')
            ->from('ShopBundle\Entity\Reservation', 'r')
            ->where(
                $query->expr()->eq('r.salesSession', ':salesSession')
            )
            ->orderBy('r.person', 'ASC')
            ->setParameter('salesSession', $salesSession)
            ->getQuery();
    }

    public function getTotalByProductBySalesQuery($salesSession)
    {
        $q = 'SELECT p.id, p.name, SUM(r.amount) FROM shop.reservations as r INNER JOIN shop.products as p ON r.product=p.id WHERE r.session=:sessId GROUP BY p.id';
        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($q);
        $stmt->bindValue('sessId', $salesSession->getId());
        $stmt->execute();
        $res = $stmt->fetchAll();

        return $res;
    }

    /**
     * @param  Person $person
     * @return int
     */
    public function getNoShowSessionCount($person)
    {
        $query = $this->getEntityManager()->createQueryBuilder();

        return $query->select($query->expr()->countDistinct('r.salesSession'))
            ->from('ShopBundle\Entity\Reservation', 'r')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('r.person', ':person'),
                    $query->expr()->eq('r.noShow', ':true')
                )
            )
            ->setParameter('person', $person)
            ->setParameter('true', true)
            ->getQuery()
            ->getResult()[0][1];
    }
}

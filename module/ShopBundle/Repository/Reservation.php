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
 * Reservation
 */
class Reservation extends EntityRepository
{
    public function getAllCurrentReservationsByPersonId($person)
    {
        $query = $this->getEntityManager()->createQueryBuilder();

        return $query->select('r')
            ->from('ShopBundle\Entity\Reservation', 'r')
            ->innerJoin('ShopBundle\Entity\SalesSession', 'ss', $query->expr()->eq('r.salesSession', 'ss.id'))
            ->where(
                $query->expr()->andX(
                    $query->expr()->lt('ss.endDate', ':now'),
                    $query->expr()->eq('r.person', ':person')
                )
            )
            ->orderBy('ss.startDate', 'ASC')
            ->setParameter('now', new DateTime())
            ->setParameter('person', $person)
            ->getQuery()
            ->getResult();
    }
}

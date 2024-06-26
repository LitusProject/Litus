<?php

namespace LogisticsBundle\Repository\Reservation;

use CommonBundle\Entity\User\Person;
use DateTime;
use LogisticsBundle\Entity\Reservation\Resource as ResourceEntity;

/**
 * Piano
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Piano extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    public function findAllActiveQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('r')
            ->from('LogisticsBundle\Entity\Reservation\Piano', 'r')
            ->where(
                $query->expr()->gte('r.endDate', ':start')
            )
            ->setParameter('start', new DateTime())
            ->orderBy('r.startDate', 'ASC')
            ->getQuery();
    }

    public function findAllOldQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('r')
            ->from('LogisticsBundle\Entity\Reservation\Piano', 'r')
            ->where(
                $query->expr()->lt('r.endDate', ':end')
            )
            ->setParameter('end', new DateTime())
            ->orderBy('r.startDate', 'DESC')
            ->getQuery();
    }

    public function findAllByDatesQuery(DateTime $start, DateTime $end)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('r')
            ->from('LogisticsBundle\Entity\Reservation\Piano', 'r')
            ->where(
                $query->expr()->orx(
                    $query->expr()->andx(
                        $query->expr()->gte('r.startDate', ':start'),
                        $query->expr()->lte('r.startDate', ':end')
                    ),
                    $query->expr()->andx(
                        $query->expr()->gte('r.endDate', ':start'),
                        $query->expr()->lte('r.endDate', ':end')
                    )
                )
            )
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery();
    }

    public function findAllConfirmedByDatesAndPersonQuery(DateTime $start, DateTime $end, Person $person)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('r')
            ->from('LogisticsBundle\Entity\Reservation\Piano', 'r')
            ->where(
                $query->expr()->andX(
                    $query->expr()->orx(
                        $query->expr()->andx(
                            $query->expr()->gte('r.startDate', ':start'),
                            $query->expr()->lte('r.startDate', ':end')
                        ),
                        $query->expr()->andx(
                            $query->expr()->gte('r.endDate', ':start'),
                            $query->expr()->lte('r.endDate', ':end')
                        )
                    ),
                    $query->expr()->eq('r.player', ':person'),
                    $query->expr()->eq('r.confirmed', 'true')
                )
            )
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('person', $person)
            ->getQuery();
    }

    public function findAllByDatesAndPersonQuery(DateTime $start, DateTime $end, Person $person)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('r')
            ->from('LogisticsBundle\Entity\Reservation\Piano', 'r')
            ->where(
                $query->expr()->andX(
                    $query->expr()->orx(
                        $query->expr()->andx(
                            $query->expr()->gte('r.startDate', ':start'),
                            $query->expr()->lte('r.startDate', ':end')
                        ),
                        $query->expr()->andx(
                            $query->expr()->gte('r.endDate', ':start'),
                            $query->expr()->lte('r.endDate', ':end')
                        )
                    ),
                    $query->expr()->eq('r.player', ':person')
                )
            )
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('person', $person)
            ->getQuery();
    }

    public function isTimeInExistingReservation(DateTime $date, $isStart)
    {
        $query = $this->getEntityManager()->createQueryBuilder();

        if ($isStart) {
            $where = $query->expr()->andX(
                $query->expr()->lte('r.startDate', ':date'),
                $query->expr()->gt('r.endDate', ':date'),
                $query->expr()->eq('r.confirmed', 'true')
            );
        } else {
            $where = $query->expr()->andX(
                $query->expr()->lt('r.startDate', ':date'),
                $query->expr()->gte('r.endDate', ':date'),
                $query->expr()->eq('r.confirmed', 'true')
            );
        }

        $resultSet = $query->select('r')
            ->from('LogisticsBundle\Entity\Reservation\Piano', 'r')
            ->where(
                $where
            )
            ->setParameter('date', $date)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        return isset($resultSet[0]);
    }

    /**
     * Finds all resources conflicting with the given start and end date for the given resource. Additionally, one id can be ignored to avoid conflicts with
     * the resource itself.
     *
     * @param  DateTime       $startDate
     * @param  DateTime       $endDate
     * @param  ResourceEntity $resource
     * @param  integer        $ignoreId
     * @return \Doctrine\ORM\Query
     */
    public function findAllConflictingIgnoringIdQuery(DateTime $startDate, DateTime $endDate, ResourceEntity $resource, $ignoreId)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('r')
            ->from('LogisticsBundle\Entity\Reservation\Piano', 'r')
            ->where(
                $query->expr()->andx(
                    $query->expr()->eq('r.resource', ':resource'),
                    $query->expr()->lt('r.startDate', ':end_date'),
                    $query->expr()->gt('r.endDate', ':start_date'),
                    $query->expr()->neq('r.id', ':id'),
                    $query->expr()->eq('r.confirmed', 'true')
                )
            )
            ->setParameter('resource', $resource)
            ->setParameter('start_date', $startDate)
            ->setParameter('end_date', $endDate)
            ->setParameter('id', $ignoreId)
            ->getQuery();
    }
}

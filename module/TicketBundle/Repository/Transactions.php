<?php

namespace TicketBundle\Repository;

class Transactions extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    public function findAllSinceDateQuery($date)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('t')
            ->from('TicketBundle\Entity\Transactions', 't')
            ->where(
                $query->expr()->gte('t.time', ':date')
            )
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
    }

    public function findAllOnDateQuery($date)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('t')
            ->from('TicketBundle\Entity\Transactions', 't')
            ->where(
                $query->expr()->eq('t.time->format("d-m-Y")', ':date')
            )
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
    }
}

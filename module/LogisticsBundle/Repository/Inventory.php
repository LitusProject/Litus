<?php

namespace LogisticsBundle\Repository;

use Doctrine\ORM\Query;

class Inventory extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    /**
     * @return \Doctrine\ORM\Query
     */
    public function findAllQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('i')
            ->from('LogisticsBundle\Entity\Inventory', 'i')
            ->orderBy('i.name', 'ASC')
            ->getQuery();
    }

    /**
     * @return Query
     */
    public function findAllNotZeroQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('i')
            ->from('LogisticsBundle\Entity\Inventory', 'i')
            ->where(
                $query->expr()->gt('i.amount', 0)
            )
            ->orderBy('i.name', 'ASC')
            ->getQuery();
    }
}

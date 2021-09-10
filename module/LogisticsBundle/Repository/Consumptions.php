<?php

namespace LogisticsBundle\Repository;

use CommonBundle\Entity\User\Person\Academic;

class Consumptions extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    public function findAllActiveQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('r')
            ->from('LogisticsBundle\Entity\Consumptions', 'r')
            ->where(
                $query->expr()->gte('r.getAcademic()', ':start')
            )
            ->setParameter('start', new Academic())
            ->orderBy('r.id')
            ->getQuery();
    }
}
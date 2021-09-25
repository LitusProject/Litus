<?php

namespace LogisticsBundle\Repository;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\User\Person\Academic;
use CommonBundle\Repository\User\Person;

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
            ->setParameter('start', new Person())
            ->orderBy('r.id')
            ->getQuery();
    }

    public function findAllByUsernameQuery($username)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('p')
            ->from('LogisticsBundle\Entity\Consumptions', 'p')
            ->where(
                $query->expr()->like('p.userName', ':username')
            )
            ->setParameter('username', '%' . strtolower($username) . '%')
            ->getQuery();
    }
}
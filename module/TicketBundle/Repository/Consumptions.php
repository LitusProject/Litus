<?php

namespace TicketBundle\Repository;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\User\Person\Academic;
use CommonBundle\Repository\User\Person;

class Consumptions extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    public function findAllActiveQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('r')
            ->from('TicketBundle\Entity\Consumptions', 'r')
            ->where(
                $query->expr()->gte('r.getAcademic()', ':start')
            )
            ->setParameter('start', new Person())
            ->orderBy('r.id')
            ->getQuery();
    }

    public function findAllByUserNameQuery($username)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('p')
            ->from('TicketBundle\Entity\Consumptions', 'p')
            ->where(
                $query->expr()->like($query->expr()->lower('p.username'), ':username')
            )
            ->setParameter('username', '%' . strtolower($username) . '%')
            ->getQuery();
    }

    public function findAllByNameQuery($name)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('p')
            ->from('TicketBundle\Entity\Consumptions', 'p')
            ->where(
                $query->expr()->like($query->expr()->lower('p.name'), ':name')
            )
            ->setParameter('name', '%' . strtolower($name) . '%')
            ->getQuery();
    }
}
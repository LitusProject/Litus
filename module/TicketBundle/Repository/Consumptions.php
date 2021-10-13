<?php

namespace TicketBundle\Repository;

class Consumptions extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
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

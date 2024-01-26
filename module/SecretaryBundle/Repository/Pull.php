<?php

namespace SecretaryBundle\Repository;

class Pull extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    public function findAllQuery(): \Doctrine\ORM\Query
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('p')
            ->from('SecretaryBundle\Entity\Pull', 'p')
            ->getQuery();
    }

    public function findAllAvailableQuery(): \Doctrine\ORM\Query
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('p')
            ->from('SecretaryBundle\Entity\Pull', 'p')
            ->where(
                $query->expr()->eq('p.available', 'true')
            )
            ->getQuery();
    }
}

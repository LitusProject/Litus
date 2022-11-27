<?php

namespace SecretaryBundle\Repository;

class Pull extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    public function findAllQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('p')
            ->from('SecretaryBundle\Entity\Pull', 'p')
            ->getQuery();
    }
}
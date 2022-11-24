<?php

namespace CommonBundle\Repository\User;

class Preference extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    /**
     * @param  integer $id
     * @return \CommonBundle\Entity\User\Preference|null
     */
    public function findOneById($id)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('p')
            ->from('CommonBundle\Entity\User\Preference', 'p')
            ->where(
                $query->expr()->eq('p.id', ':id')
            )
            ->setParameter('id', $id)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
<?php

namespace CommonBundle\Repository\User;

class PreferenceMapping extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    /**
     * @param  integer $id
     * @return \CommonBundle\Entity\User\PreferenceMapping|null
     */
    public function findOneById($id)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('p')
            ->from('CommonBundle\Entity\User\PreferenceMapping', 'p')
            ->where(
                $query->expr()->eq('p.id', ':id')
            )
            ->setParameter('id', $id)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
<?php

namespace BrBundle\Repository;

use BrBundle\Entity\Company;

class Communication extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    /**
     * @param Company $company
     * @return \Doctrine\ORM\Query
     */
    public function findAllByCompany(Company $company)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('e')
            ->from('BrBundle\Entity\Communication', 'e')
            ->where(
                $query->expr()->eq('e.company', ':company')
            )
            ->setParameter('company', $company)
            ->getQuery();
    }

    public function findAllActiveQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('r')
            ->from('BrBundle\Entity\Communication', 'r')
            ->where(
                $query->expr()->gte('r.getCompany()', ':start')
            )
            ->setParameter('start', new Company())
            ->orderBy('r.date')
            ->getQuery();
    }
}

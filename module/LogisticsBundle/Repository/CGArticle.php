<?php

namespace LogisticsBundle\Repository;

/**
 * CGArticle
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CGArticle extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    /**
     * @return array
     */
    public function findAll(): array
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('a')
            ->from('LogisticsBundle\Entity\CGArticle', 'a')
            ->orderBy('a.category', 'ASC')
            ->addOrderBy('a.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param  string $name
     * @return array
     */
    public function findAllByName(string $name): array
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('a')
            ->from('LogisticsBundle\Entity\CGArticle', 'a')
            ->where(
                $query->expr()->like($query->expr()->lower('a.name'), ':name')
            )
            ->setParameter('name', '%' . strtolower($name) . '%')
            ->orderBy('a.category', 'ASC')
            ->addOrderBy('a.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param  string $brand
     * @return array
     */
    public function findAllByBrand(string $brand): array
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('a')
            ->from('LogisticsBundle\Entity\CGArticle', 'a')
            ->where(
                $query->expr()->like($query->expr()->lower('a.brand'), ':brand')
            )
            ->setParameter('brand', '%' . strtolower($brand) . '%')
            ->orderBy('a.category', 'ASC')
            ->addOrderBy('a.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
<?php

namespace NewsBundle\Repository\Node;

use DateTime;

/**
 * News
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class News extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    public function findAllQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('n')
            ->from('NewsBundle\Entity\Node\News', 'n')
            ->orderBy('n.creationTime', 'DESC')
            ->getQuery();
    }

    public function findAllSiteQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('n')
            ->from('NewsBundle\Entity\Node\News', 'n')
            ->where(
                $query->expr()->orX(
                    $query->expr()->gte('n.endDate', ':now'),
                    $query->expr()->isNull('n.endDate')
                )
            )
            ->setParameter('now', new DateTime())
            ->orderBy('n.creationTime', 'DESC')
            ->getQuery();
    }

    public function findApiQuery(DateTime $maxAge)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('n')
            ->from('NewsBundle\Entity\Node\News', 'n')
            ->where(
                $query->expr()->andX(
                    $query->expr()->gt('n.creationTime', ':maxAge'),
                    $query->expr()->orX(
                        $query->expr()->gte('n.endDate', ':now'),
                        $query->expr()->isNull('n.endDate')
                    )
                )
            )
            ->setParameter('now', new DateTime())
            ->setParameter('maxAge', $maxAge)
            ->orderBy('n.creationTime', 'DESC')
            ->getQuery();
    }

    public function findNbSiteQuery($nbResults, DateTime $maxAge)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('n')
            ->from('NewsBundle\Entity\Node\News', 'n')
            ->where(
                $query->expr()->andX(
                    $query->expr()->gt('n.creationTime', ':maxAge'),
                    $query->expr()->orX(
                        $query->expr()->gte('n.endDate', ':now'),
                        $query->expr()->isNull('n.endDate')
                    )
                )
            )
            ->setParameter('now', new DateTime())
            ->setParameter('maxAge', $maxAge)
            ->orderBy('n.creationTime', 'DESC')
            ->setMaxResults($nbResults)
            ->getQuery();
    }
}

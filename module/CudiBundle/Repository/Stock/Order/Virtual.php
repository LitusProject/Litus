<?php

namespace CudiBundle\Repository\Stock\Order;

use CudiBundle\Entity\Sale\Article;
use CudiBundle\Entity\Stock\Period;

/**
 * Virtual
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Virtual extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    /**
     * @param  Period  $period
     * @param  Article $article
     * @return integer
     */
    public function findNbByPeriodAndArticle(Period $period, Article $article)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select('SUM(v.number)')
            ->from('CudiBundle\Entity\Stock\Order\Virtual', 'v')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('v.article', ':article'),
                    $query->expr()->gt('v.dateCreated', ':startDate'),
                    $period->isOpen() ? '1=1' : $query->expr()->lt('v.dateCreated', ':endDate')
                )
            )
            ->setParameter('article', $article->getId())
            ->setParameter('startDate', $period->getStartDate());

        if (!$period->isOpen()) {
            $query->setParameter('endDate', $period->getEndDate());
        }

        $resultSet = $query->getQuery()
            ->getSingleScalarResult();

        if ($resultSet == null) {
            return 0;
        }

        return $resultSet;
    }

    /**
     * @param  Period  $period
     * @param  Article $article
     * @return \Doctrine\ORM\Query
     */
    public function findAllByPeriodAndArticleQuery(Period $period, Article $article)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select('v')
            ->from('CudiBundle\Entity\Stock\Order\Virtual', 'v')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('v.article', ':article'),
                    $query->expr()->gt('v.dateCreated', ':startDate'),
                    $period->isOpen() ? '1=1' : $query->expr()->lt('v.dateCreated', ':endDate')
                )
            )
            ->setParameter('article', $article->getId())
            ->setParameter('startDate', $period->getStartDate());

        if (!$period->isOpen()) {
            $query->setParameter('endDate', $period->getEndDate());
        }

        return $query->getQuery();
    }
}

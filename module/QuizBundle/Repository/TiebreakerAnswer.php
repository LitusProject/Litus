<?php

namespace QuizBundle\Repository;

use QuizBundle\Entity\Tiebreaker as TiebreakerEntity;

/**
 * TiebreakerAnswer
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TiebreakerAnswer extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    /**
     * Gets all tiebreaker answers belonging to a tiebreaker
     * @param TiebreakerEntity $tiebreaker The tiebreaker the answers must belong to
     */
    public function findAllByTiebreakerQuery(TiebreakerEntity $tiebreaker)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('a')
            ->from('QuizBundle\Entity\TiebreakerAnswer', 'a')
            ->where(
                $query->expr()->eq('a.tiebreaker', ':tiebreaker')
            )
            ->setParameter('tiebreaker', $tiebreaker)
            ->getQuery();
    }
}

<?php

namespace QuizBundle\Repository;

use QuizBundle\Entity\Quiz as QuizEntity;

/**
 * Team
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Team extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    /**
     * Gets all teams belonging to a quiz
     * @param QuizEntity $quiz The team the rounds must belong to
     */
    public function findAllByQuizQuery(QuizEntity $quiz)
    {
        $query = $this->getEntityManager()->createQueryBuilder();

        return $query->select('t')
            ->from('QuizBundle\Entity\Team', 't')
            ->where(
                $query->expr()->eq('t.quiz', ':quiz')
            )
            ->orderBy('t.number', 'ASC')
            ->setParameter('quiz', $quiz->getId())
            ->getQuery();
    }

    /**
     * Gets the number for the next team in the quiz
     * @param  QuizEntity $quiz
     * @return integer
     */
    public function getNextTeamNumberForQuiz(QuizEntity $quiz)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('MAX(t.number)')
            ->from('QuizBundle\Entity\Team', 't')
            ->where(
                $query->expr()->eq('t.quiz', ':quiz')
            )
            ->setParameter('quiz', $quiz->getId())
            ->getQuery()
            ->getSingleScalarResult();

        if ($resultSet === null) {
            return 1;
        }

        return $resultSet + 1;
    }
}

<?php

namespace DoorBundle\Repository;

use DateTime;

/**
 * Rule
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Rule extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    /**
     * @return array
     */
    public function findAll()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('r')
            ->from('DoorBundle\Entity\Rule', 'r')
            ->where(
                $query->expr()->gte('r.endDate', ':now')
            )
            ->setParameter('now', (new DateTime())->setTime(0, 0))
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $academic
     * @return float|integer|mixed|string
     */
    public function findAllByAcademic($academic)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('r')
            ->from('DoorBundle\Entity\Rule', 'r')
            ->where(
                $query->expr()->gte('r.endDate', ':now'),
                $query->expr()->eq('r.academic', ':academic'),
            )
            ->setParameter('now', (new DateTime())->setTime(0, 0))
            ->setParameter('academic', $academic)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array
     */
    public function findOld()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('r')
            ->from('DoorBundle\Entity\Rule', 'r')
            ->where(
                $query->expr()->lte('r.endDate', ':now')
            )
            ->setParameter('now', new DateTime())
            ->getQuery()
            ->getResult();
    }
}

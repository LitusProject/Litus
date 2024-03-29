<?php

namespace CudiBundle\Repository\Log\Article\Sale;

use DateTime;

/**
 * Bookable
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Bookable extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    /**
     * @param  DateTime $date
     * @return \Doctrine\ORM\Query
     */
    public function findAllAfterQuery(DateTime $date)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('l')
            ->from('CudiBundle\Entity\Log\Article\Sale\Bookable', 'l')
            ->where(
                $query->expr()->gt('l.timestamp', ':date')
            )
            ->setParameter('date', $date)
            ->getQuery();
    }
}

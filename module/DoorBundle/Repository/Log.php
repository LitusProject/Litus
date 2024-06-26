<?php

namespace DoorBundle\Repository;

use DateTime;

/**
 * Log
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Log extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    /**
     * @param  DateTime $since
     * @return array
     */
    public function findAllSince(DateTime $since)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('l')
            ->from('DoorBundle\Entity\Log', 'l')
            ->where(
                $query->expr()->gt('l.timestamp', ':since')
            )
            ->setParameter('since', $since)
            ->getQuery()
            ->getResult();
    }
}

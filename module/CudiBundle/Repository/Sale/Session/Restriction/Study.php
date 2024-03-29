<?php

namespace CudiBundle\Repository\Sale\Session\Restriction;

use CudiBundle\Entity\Sale\Session;

/**
 * Study
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Study extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    /**
     * @param  Session $session
     * @return \CudiBundle\Entity\Sale\Session\Restriction\Study|null
     */
    public function findOneBySessionAndType(Session $session)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('r')
            ->from('CudiBundle\Entity\Sale\Session\Restriction\Study', 'r')
            ->where(
                $query->expr()->eq('r.session', ':session')
            )
            ->setParameter('session', $session)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}

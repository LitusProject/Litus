<?php

namespace CudiBundle\Repository\Sale\Session\Restriction;

use CudiBundle\Entity\Sale\Session;

/**
 * Name
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Name extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    /**
     * @param  Session $session
     * @return \CudiBundle\Entity\Sale\Session\Restriction\Name|null
     */
    public function findOneBySessionAndType(Session $session)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('r')
            ->from('CudiBundle\Entity\Sale\Session\Restriction\Name', 'r')
            ->where(
                $query->expr()->eq('r.session', ':session')
            )
            ->setParameter('session', $session)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}

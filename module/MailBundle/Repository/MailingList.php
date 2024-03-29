<?php

namespace MailBundle\Repository;

/**
 * MailingList
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MailingList extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    public function findAllByNameQuery($name)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('l')
            ->from('MailBundle\Entity\MailingList\Named', 'l')
            ->where(
                $query->expr()->like($query->expr()->lower('l.name'), ':name')
            )
            ->setParameter('name', '%' . strtolower($name) . '%')
            ->getQuery();
    }
}

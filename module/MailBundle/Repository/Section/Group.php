<?php

namespace MailBundle\Repository\Section;

class Group extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    public function findAllQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('a')
            ->from('MailBundle\Entity\Section\Group', 'a')
            ->getQuery();
    }
}
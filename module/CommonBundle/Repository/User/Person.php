<?php

namespace CommonBundle\Repository\User;

use CommonBundle\Entity\Acl\Role;

/**
 * Person
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Person extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    /**
     * @param  string $name
     * @return \Doctrine\ORM\Query
     */
    public function findAllByNameQuery($name)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('p')
            ->from('CommonBundle\Entity\User\Person', 'p')
            ->where(
                $query->expr()->orX(
                    $query->expr()->like(
                        $query->expr()->concat(
                            $query->expr()->lower($query->expr()->concat('p.firstName', "' '")),
                            $query->expr()->lower('p.lastName')
                        ),
                        ':name'
                    ),
                    $query->expr()->like(
                        $query->expr()->concat(
                            $query->expr()->lower($query->expr()->concat('p.lastName', "' '")),
                            $query->expr()->lower('p.firstName')
                        ),
                        ':name'
                    )
                )
            )
            ->setParameter('name', '%' . strtolower($name) . '%')
            ->getQuery();
    }

    /**
     * @param  Role $role
     * @return \Doctrine\ORM\Query
     */
    public function findAllByRoleQuery(Role $role)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('p')
            ->from('CommonBundle\Entity\User\Person', 'p')
            ->innerJoin('p.roles', 'r')
            ->where(
                $query->expr()->eq('r.name', ':name')
            )
            ->setParameter('name', $role->getName())
            ->getQuery();
    }

    /**
     * @param  string $username
     * @return \Doctrine\ORM\Query
     */
    public function findAllByUsernameQuery($username)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('p')
            ->from('CommonBundle\Entity\User\Person', 'p')
            ->where(
                $query->expr()->like('p.username', ':username')
            )
            ->setParameter('username', '%' . strtolower($username) . '%')
            ->getQuery();
    }

    /**
     * @param  string $username
     * @return \CommonBundle\Entity\User\Person|null
     */
    public function findOneByUsername($username)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('p')
            ->from('CommonBundle\Entity\User\Person', 'p')
            ->where(
                $query->expr()->eq('p.username', ':username')
            )
            ->setParameter('username', strtolower($username))
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($resultSet) {
            return $resultSet;
        }

        $barcode = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Barcode')
            ->findOneByBarcode($username);

        if ($barcode) {
            return $barcode->getPerson();
        }

        return null;
    }
}

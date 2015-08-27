<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace ShopBundle\Repository;

use CommonBundle\Component\Doctrine\ORM\EntityRepository;

/**
 * Product
 */
class Product extends EntityRepository
{
    /**
	 * @return \Doctrine\ORM\Query
	 */
    public function findAllQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('p')
            ->from('ShopBundle\Entity\Product', 'p')
            ->orderBy('p.name', 'ASC')
            ->getQuery();

        return $resultSet;
    }

    /**
	 * @param  string $name
	 * @return \Doctrine\ORM\Query
	 */
    public function findAllByNameQuery($name)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('p')
            ->from('ShopBundle\Entity\Product', 'p')
            ->where(
                $query->expr()->like($query->expr()->lower('p.name'), ':name')
            )
            ->orderBy('p.name', 'ASC')
            ->setParameter('name', '%' . strtolower($name) . '%')
            ->getQuery();

        return $resultSet;
    }

    /**
	 * @return array
	 */
    public function findAllAvailable()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('p')
            ->from('ShopBundle\Entity\Product', 'p')
            ->where(
                $query->expr()->eq('p.available', ':available')
            )
            ->orderBy('p.name', 'ASC')
            ->setParameter('available', true)
            ->getQuery()
            ->getResult();

        return $resultSet;
    }
}

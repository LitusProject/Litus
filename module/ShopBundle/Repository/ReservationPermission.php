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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace ShopBundle\Repository;

/**
 * Reservation Permission
 * @author Floris Kint <floris.kint@litus.cc>
 */
class ReservationPermission extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    /**
     * @param $name
     * @return \Doctrine\ORM\Query
     */
    public function findByNameQuery($name)
    {
        $query = $this->getEntityManager()->createQueryBuilder();

        return $query->select('rp')
            ->from('ShopBundle\Entity\ReservationPermission', 'rp')
            ->join('rp.person', 'p')
            ->where(
                $query->expr()->orX(
                    $query->expr()->like($query->expr()->lower($query->expr()->concat('p.firstName', $query->expr()->concat("' '", 'p.lastName'))), ':name'),
                    $query->expr()->like($query->expr()->lower($query->expr()->concat('p.lastName', $query->expr()->concat("' '", 'p.firstName'))), ':name')
                )
            )
            ->orderBy('p.id', 'ASC')
            ->setParameter('name', '%' . strtolower($name) . '%')
            ->getQuery();
    }

    /**
     * @return \Doctrine\ORM\Query
     */
    public function findAllQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();

        return $query->select('rp')
            ->from('ShopBundle\Entity\ReservationPermission', 'rp')
            ->getQuery();
    }
}

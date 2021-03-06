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

namespace ApiBundle\Repository;

use DateTime;

/**
 * Key
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Key extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    /**
     * @return \Doctrine\ORM\Query
     */
    public function findAllActiveQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('k')
            ->from('ApiBundle\Entity\Key', 'k')
            ->where(
                $query->expr()->gt('k.expirationTime', ':now')
            )
            ->setParameter('now', new DateTime())
            ->getQuery();
    }

    /**
     * @return \ApiBundle\Entity\Key|null
     */
    public function findOneActiveByCode($code)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('k')
            ->from('ApiBundle\Entity\Key', 'k')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('k.code', ':code'),
                    $query->expr()->gt('k.expirationTime', ':now')
                )
            )
            ->setParameter('code', $code)
            ->setParameter('now', new DateTime())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}

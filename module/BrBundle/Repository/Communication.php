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

namespace BrBundle\Repository;

use BrBundle\Entity\Company;

class Communication extends \CommonBundle\Component\Doctrine\ORM\EntityRepository {
    /**
     * @param Company $company
     * @return \Doctrine\ORM\Query
     */
    public function findAllByCompany(Company $company)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('e')
            ->from('BrBundle\Entity\Communication', 'e')
            ->where(
                $query->expr()->eq('e.company', ':company')
            )
            ->setParameter('company', $company)
            ->getQuery();
    }

    public function findAllActiveQuery() {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('r')
            ->from('BrBundle\Entity\Communication', 'r')
            ->where(
                $query->expr()->gte('r.getCompany()', ':start')
            )
            ->setParameter('start', new Company())
            ->orderBy('r.date')
            ->getQuery();
    }
}
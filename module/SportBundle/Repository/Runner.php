<?php

namespace SportBundle\Repository;

use CommonBundle\Entity\General\AcademicYear;

/**
 * Runner
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Runner extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    public function findOneByUniversityIdentification($universityIdentification)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('r')
            ->from('SportBundle\Entity\Runner', 'r')
            ->innerJoin('r.academic', 'a')
            ->where(
                $query->expr()->eq('a.universityIdentification', ':universityIdentification')
            )
            ->setParameter('universityIdentification', $universityIdentification)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllWithoutIdentificationQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('r')
            ->from('SportBundle\Entity\Runner', 'r')
            ->where(
                $query->expr()->andX(
                    $query->expr()->isNull('r.runnerIdentification'),
                    $query->expr()->isNull('r.academic')
                )
            )
            ->getQuery();
    }

    public function findAllByAcademicYearQuery(AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('g')
            ->from('SportBundle\Entity\Runner', 'g')
            ->where(
                $query->expr()->eq('g.academicYear', ':academicYear')
            )
            ->setParameter('academicYear', $academicYear)
            ->getQuery();
    }
}

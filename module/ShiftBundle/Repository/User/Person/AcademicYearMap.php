<?php

namespace ShiftBundle\Repository\User\Person;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\User\Person;

/**
 * AcademicYearMap
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AcademicYearMap extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    /**
     * @param  AcademicYear $academicYear
     * @param  Person       $person
     * @return \Doctrine\ORM\Query
     */
    public function findOneByPersonAndAcademicYear(Person $person, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('i')
            ->from('ShiftBundle\Entity\User\Person\AcademicYearMap', 'i')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('i.person', ':person'),
                    $query->expr()->eq('i.academicYear', ':academicYear')
                )
            )
            ->setParameter('person', $person->getId())
            ->setParameter('academicYear', $academicYear->getId())
            ->getQuery()
            ->getOneOrNullResult();
    }
}

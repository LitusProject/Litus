<?php

namespace SyllabusBundle\Repository\Group;

use CommonBundle\Entity\General\AcademicYear;
use SyllabusBundle\Entity\Group as GroupEntity;
use SyllabusBundle\Entity\Study as StudyEntity;

/**
 * StudyMap
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StudyMap extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    public function findAllByGroupAndAcademicYearQuery(GroupEntity $group, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('m', 's')
            ->from('SyllabusBundle\Entity\Group\StudyMap', 'm')
            ->innerJoin('m.study', 's')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('m.group', ':group'),
                    $query->expr()->eq('s.academicYear', ':academicYear')
                )
            )
            ->setParameter('group', $group)
            ->setParameter('academicYear', $academicYear)
            ->getQuery();
    }

    public function findOneByStudyGroup(StudyEntity $study, GroupEntity $group)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('m')
            ->from('SyllabusBundle\Entity\Group\StudyMap', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('m.group', ':group'),
                    $query->expr()->eq('m.study', ':study')
                )
            )
            ->setParameter('group', $group)
            ->setParameter('study', $study)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findMapsFromStudyQuery(StudyEntity $study)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('m')
            ->from('SyllabusBundle\Entity\Group\StudyMap', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('m.study', ':study')
                )
            )
            ->setParameter('study', $study)
            ->getQuery();
    }
}

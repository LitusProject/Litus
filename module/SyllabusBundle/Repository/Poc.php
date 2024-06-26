<?php

namespace SyllabusBundle\Repository;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\User\Person\Academic;
use SyllabusBundle\Entity\Group as GroupEntity;

/**
 * Group
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Poc extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    /**
     * @return \Doctrine\ORM\Query
     */
    public function findAllQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('p', 'g')
            ->from('SyllabusBundle\Entity\Poc', 'p')
            ->innerJoin('p.groupId', 'g')
            ->orderBy('g.name', 'ASC')
            ->getQuery();
    }

    /**
     * @param  AcademicYear $academicYear
     * @return \Doctrine\ORM\Query
     */
    public function findAllByAcademicYearQuery(AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('p', 'g')
            ->from('SyllabusBundle\Entity\poc', 'p')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('p.academicYear', ':academicYear'),
                    $query->expr()->neq('p.indicator', 'true')
                )
            )
            ->setParameter('academicYear', $academicYear)
            ->innerJoin('p.groupId', 'g')
            ->orderBy('g.name', 'ASC')
            ->getQuery();
    }

    /**
     * @param  GroupEntity  $groupId
     * @param  AcademicYear $academicYear
     * @return \Doctrine\ORM\Query
     */
    public function findPocersFromGroupAndAcademicYearQuery(GroupEntity $groupId, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();

        return $query->select('p', 'g')
            ->from('SyllabusBundle\Entity\poc', 'p')
            ->where(
                $query->expr()->andX(
                    $query->expr()->neq('p.indicator', 'true'),
                    $query->expr()->eq('p.groupId', ':groupId'),
                    $query->expr()->eq('p.academicYear', ':academicYear')
                )
            )
            ->setParameter('academicYear', $academicYear)
            ->setParameter('groupId', $groupId)
            ->innerJoin('p.groupId', 'g')
            ->orderBy('g.name', 'ASC')
            ->getQuery();
    }

    /**
     * @param  GroupEntity  $groupId
     * @param  AcademicYear $academicYear
     * @return \Doctrine\ORM\Query
     */
    public function findPocersFromGroupAndAcademicYearWithIndicatorQuery(GroupEntity $groupId, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();

        return $query->select('p', 'g')
            ->from('SyllabusBundle\Entity\poc', 'p')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('p.groupId', ':groupId'),
                    $query->expr()->eq('p.academicYear', ':academicYear')
                )
            )
            ->setParameter('academicYear', $academicYear)
            ->setParameter('groupId', $groupId)
            ->innerJoin('p.groupId', 'g')
            ->orderBy('g.name', 'ASC')
            ->getQuery();
    }

    /**
     * @param  Academic     $academic
     * @param  AcademicYear $academicYear
     * @return \Doctrine\ORM\Query
     */
    public function findPocersByAcademicAndAcademicYearQuery(Academic $academic, AcademicYear $academicYear)
    {
        $studyEnrollments = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\Enrollment\Study')
            ->findAllByAcademicAndAcademicYear($academic, $academicYear);
        $idsOfStudiesOfEnrollment = array(0);

        foreach ($studyEnrollments as $studyEnrollment) {
            $idsOfStudiesOfEnrollment[] = $studyEnrollment->getStudy()->getId();
        }

        $query = $this->getEntityManager()->createQueryBuilder();

        $studyMaps = $query->select('m')
            ->from('SyllabusBundle\Entity\Group\StudyMap', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->in('m.study', $idsOfStudiesOfEnrollment)
                )
            )
            ->getQuery()
            ->getResult();

        $idsOfGroup = array(0);
        foreach ($studyMaps as $studyMap) {
            $idsOfGroup[] = $studyMap->getGroup()->getId();
        }

        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('p', 'g')
            ->from('SyllabusBundle\Entity\poc', 'p')
            ->where(
                $query->expr()->andX(
                    $query->expr()->neq('p.indicator', 'true'),
                    $query->expr()->in('p.groupId', $idsOfGroup),
                    $query->expr()->eq('p.academicYear', ':academicYear')
                )
            )
            ->setParameter('academicYear', $academicYear)
            ->innerJoin('p.groupId', 'g')
            ->orderBy('g.name', 'ASC')
            ->getQuery();
    }

    /**
     * @param  GroupEntity  $group
     * @param  AcademicYear $academicYear
     * @return integer
     */
    public function getNbOfPocersFromGroupEntity(GroupEntity $group, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select($query->expr()->count('p'))
            ->from('SyllabusBundle\Entity\poc', 'p')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('p.groupId', ':group'),
                    $query->expr()->eq('p.academicYear', ':academicYear'),
                    $query->expr()->neq('p.indicator', 'true')
                )
            )
            ->setParameter('group', $group)
            ->setParameter('academicYear', $academicYear)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param  AcademicYear $academicYear
     * @return array        of Pocs
     */
    public function findAllPocsWithIndicatorByAcademicYearQuery(AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('p')
            ->from('SyllabusBundle\Entity\poc', 'p')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('p.academicYear', ':academicYear'),
                    $query->expr()->eq('p.indicator', 'true')
                )
            )
            ->setParameter('academicYear', $academicYear)
            ->innerJoin('p.groupId', 'g')
            ->orderBy('g.name', 'ASC')
            ->getQuery();
    }

    /**
     * returns boolean
     */
    public function getIsPocGroup(GroupEntity $group, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select($query->expr()->count('p'))
            ->from('SyllabusBundle\Entity\poc', 'p')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('p.groupId', ':group'),
                    $query->expr()->eq('p.academicYear', ':academicYear')
                )
            )
            ->setParameter('group', $group)
            ->setParameter('academicYear', $academicYear)
            ->getQuery()
            ->getSingleScalarResult();

        return $resultSet >= 1;
    }

    /**
     * @param  AcademicYear $academicYear
     * @return array        of Groups
     */
    public function findIndicatorFromGroupAndAcademicYear(GroupEntity $group, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('p')
            ->from('SyllabusBundle\Entity\poc', 'p')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('p.academicYear', ':academicYear'),
                    $query->expr()->eq('p.indicator', 'true'),
                    $query->expr()->eq('p.groupId', ':group')
                )
            )
            ->setParameter('academicYear', $academicYear)
            ->setParameter('group', $group)
            ->innerJoin('p.groupId', 'g')
            ->orderBy('g.name', 'ASC')
            ->getQuery()
            ->getSingleResult();
    }
}

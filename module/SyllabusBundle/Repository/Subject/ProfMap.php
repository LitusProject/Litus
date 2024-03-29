<?php

namespace SyllabusBundle\Repository\Subject;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\User\Person\Academic;
use SyllabusBundle\Entity\Subject as SubjectEntity;

/**
 * ProfMap
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProfMap extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    public function findOneBySubjectAndProfAndAcademicYear(SubjectEntity $subject, Academic $prof, AcademicYear $academicYear)
    {
        return $this->findOneBySubjectIdAndProfAndAcademicYear($subject->getId(), $prof, $academicYear);
    }

    /**
     * @param integer $subjectId
     */
    public function findOneBySubjectIdAndProfAndAcademicYear($subjectId, Academic $prof, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('m')
            ->from('SyllabusBundle\Entity\Subject\ProfMap', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('m.subject', ':subject'),
                    $query->expr()->eq('m.prof', ':prof'),
                    $query->expr()->eq('m.academicYear', ':academicYear')
                )
            )
            ->setParameter('subject', $subjectId)
            ->setParameter('prof', $prof->getId())
            ->setParameter('academicYear', $academicYear->getId())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param integer $subjectId
     */
    public function findOneBySubjectIdAndAcademicYear($subjectId, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('m')
            ->from('SyllabusBundle\Entity\Subject\ProfMap', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('m.subject', ':subject'),
                    $query->expr()->eq('m.academicYear', ':academicYear')
                )
            )
            ->setParameter('subject', $subjectId)
            ->setParameter('academicYear', $academicYear->getId())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllBySubjectAndAcademicYearQuery(SubjectEntity $subject, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('m')
            ->from('SyllabusBundle\Entity\Subject\ProfMap', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('m.subject', ':subject'),
                    $query->expr()->eq('m.academicYear', ':academicYear')
                )
            )
            ->setParameter('subject', $subject->getId())
            ->setParameter('academicYear', $academicYear->getId())
            ->getQuery();
    }

    public function findAllByProfAndAcademicYearQuery(Academic $prof, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('m')
            ->from('SyllabusBundle\Entity\Subject\ProfMap', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('m.prof', ':prof'),
                    $query->expr()->eq('m.academicYear', ':academicYear')
                )
            )
            ->setParameter('prof', $prof->getId())
            ->setParameter('academicYear', $academicYear->getId())
            ->getQuery();
    }

    public function findAllByNameAndProfAndAcademicYearTypeaheadQuery($name, Academic $prof, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('m')
            ->from('SyllabusBundle\Entity\Subject\ProfMap', 'm')
            ->innerJoin('m.subject', 's')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('m.prof', ':prof'),
                    $query->expr()->eq('m.academicYear', ':academicYear'),
                    $query->expr()->like($query->expr()->lower('s.name'), ':name')
                )
            )
            ->setParameter('prof', $prof->getId())
            ->setParameter('academicYear', $academicYear->getId())
            ->setParameter('name', '%' . strtolower($name) . '%')
            ->getQuery();
    }
}

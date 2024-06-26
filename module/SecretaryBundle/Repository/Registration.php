<?php

namespace SecretaryBundle\Repository;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\General\Organization;
use CommonBundle\Entity\User\Person\Academic;
use DateTime;

/**
 * Registration
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Registration extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    public function findOneByAcademicAndAcademicYear(Academic $academic, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('r')
            ->from('SecretaryBundle\Entity\Registration', 'r')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('r.academic', ':academic'),
                    $query->expr()->eq('r.academicYear', ':academicYear')
                )
            )
            ->setParameter('academic', $academic)
            ->setParameter('academicYear', $academicYear)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllByUniversityIdentification($universityIdentification, AcademicYear $academicYear, Organization $organization = null)
    {
        $ids = array(0);
        if ($organization !== null) {
            $query = $this->getEntityManager()->createQueryBuilder();
            $resultSet = $query->select('a.id')
                ->from('CommonBundle\Entity\User\Person\Organization\AcademicYearMap', 'm')
                ->innerJoin('m.academic', 'a')
                ->where(
                    $query->expr()->andX(
                        $query->expr()->eq('m.organization', ':organization'),
                        $query->expr()->eq('m.academicYear', ':academicYear')
                    )
                )
                ->setParameter('organization', $organization)
                ->setParameter('academicYear', $academicYear)
                ->getQuery()
                ->getResult();

            foreach ($resultSet as $result) {
                $ids[] = $result['id'];
            }
        } else {
            $query = $this->getEntityManager()->createQueryBuilder();
            $resultSet = $query->select('a.id')
                ->from('CommonBundle\Entity\User\Person\Organization\AcademicYearMap', 'm')
                ->innerJoin('m.academic', 'a')
                ->where(
                    $query->expr()->eq('m.academicYear', ':academicYear')
                )
                ->setParameter('academicYear', $academicYear)
                ->getQuery()
                ->getResult();

            foreach ($resultSet as $result) {
                $ids[] = $result['id'];
            }
        }

        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('r')
            ->from('SecretaryBundle\Entity\Registration', 'r')
            ->innerJoin('r.academic', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('a.universityIdentification'), ':universityIdentification'),
                    $query->expr()->eq('r.academicYear', ':academicYear'),
                    $organization == null ? '1=1' : $query->expr()->in('a.id', $ids)
                )
            )
            ->setParameter('universityIdentification', '%' . strtolower($universityIdentification) . '%')
            ->setParameter('academicYear', $academicYear)
            ->orderBy('r.timestamp', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllByName($name, AcademicYear $academicYear, Organization $organization = null)
    {
        $ids = array(0);
        if ($organization !== null) {
            $query = $this->getEntityManager()->createQueryBuilder();
            $resultSet = $query->select('a.id')
                ->from('CommonBundle\Entity\User\Person\Organization\AcademicYearMap', 'm')
                ->innerJoin('m.academic', 'a')
                ->where(
                    $query->expr()->andX(
                        $query->expr()->eq('m.organization', ':organization'),
                        $query->expr()->eq('m.academicYear', ':academicYear')
                    )
                )
                ->setParameter('organization', $organization)
                ->setParameter('academicYear', $academicYear)
                ->getQuery()
                ->getResult();

            foreach ($resultSet as $result) {
                $ids[] = $result['id'];
            }
        }

        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('r')
            ->from('SecretaryBundle\Entity\Registration', 'r')
            ->innerJoin('r.academic', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->orX(
                        $query->expr()->like(
                            $query->expr()->concat(
                                $query->expr()->lower($query->expr()->concat('a.firstName', "' '")),
                                $query->expr()->lower('a.lastName')
                            ),
                            ':name'
                        ),
                        $query->expr()->like(
                            $query->expr()->concat(
                                $query->expr()->lower($query->expr()->concat('a.lastName', "' '")),
                                $query->expr()->lower('a.firstName')
                            ),
                            ':name'
                        )
                    ),
                    $query->expr()->eq('r.academicYear', ':academicYear'),
                    $organization == null ? '1=1' : $query->expr()->in('a.id', $ids)
                )
            )
            ->setParameter('name', '%' . strtolower($name) . '%')
            ->setParameter('academicYear', $academicYear)
            ->orderBy('r.timestamp', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllByBarcode($barcode, AcademicYear $academicYear, Organization $organization = null)
    {
        if (!is_numeric($barcode)) {
            return array();
        }

        if ($organization === null) {
            $resultSet = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Barcode')
                ->findAllByBarcode($barcode);
        } else {
            $resultSet = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Barcode')
                ->findAllByBarcodeAndOrganization($barcode, $academicYear, $organization);
        }

        $ids = array(0);
        foreach ($resultSet as $result) {
            $ids[] = $result->getPerson()->getId();
        }

        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('r')
            ->from('SecretaryBundle\Entity\Registration', 'r')
            ->where(
                $query->expr()->andX(
                    $query->expr()->in('r.academic', $ids),
                    $query->expr()->eq('r.academicYear', ':academicYear')
                )
            )
            ->setParameter('academicYear', $academicYear)
            ->orderBy('r.timestamp', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllSince(DateTime $since)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('r')
            ->from('SecretaryBundle\Entity\Registration', 'r')
            ->where(
                $query->expr()->gte('r.timestamp', ':since')
            )
            ->setParameter('since', $since)
            ->orderBy('r.timestamp', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

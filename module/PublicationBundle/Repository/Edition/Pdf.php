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

namespace PublicationBundle\Repository\Edition;

use CommonBundle\Entity\General\AcademicYear;
use PublicationBundle\Entity\Publication as PublicationEntity;

/**
 * Pdf
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Pdf extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    public function findAllByPublicationAndAcademicYearQuery(PublicationEntity $publication, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('p')
            ->from('PublicationBundle\Entity\Edition\Pdf', 'p')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('p.publication', ':publication'),
                    $query->expr()->eq('p.academicYear', ':year')
                )
            )
            ->setParameter('publication', $publication)
            ->setParameter('year', $academicYear)
            ->orderBy('p.date', 'ASC')
            ->getQuery();
    }

    public function findOneByPublicationTitleAndAcademicYear(PublicationEntity $publication, $title, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('p')
            ->from('PublicationBundle\Entity\Edition\Pdf', 'p')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('p.publication', ':publication'),
                    $query->expr()->eq('p.title', ':title'),
                    $query->expr()->eq('p.academicYear', ':year')
                )
            )
            ->setParameter('publication', $publication)
            ->setParameter('title', $title)
            ->setParameter('year', $academicYear)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}

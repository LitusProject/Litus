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
 *
 * @license http://litus.cc/LICENSE
 */

namespace SyllabusBundle\Controller\Console;

use CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Entity\General\AcademicYear as AcademicYearEntity,
    DateInterval,
    DateTime;

/**
 * InstallController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class InstallController extends \CommonBundle\Component\Controller\ConsoleController\InstallController
{
    protected function postInstall()
    {
        $this->_installAcademicYear();
    }

    private function _installAcademicYear()
    {
        $now = new DateTime('now');
        $startAcademicYear = AcademicYear::getStartOfAcademicYear(
            $now
        );

        $academicYear = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByUniversityStart($startAcademicYear);

        $organizationStart = str_replace(
            '{{ year }}',
            $startAcademicYear->format('Y'),
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('start_organization_year')
        );
        $organizationStart = new DateTime($organizationStart);

        if (null === $academicYear) {
            $academicYear = new AcademicYearEntity($organizationStart, $startAcademicYear);
            $this->getEntityManager()->persist($academicYear);
            $this->getEntityManager()->flush();
        }

        $organizationStart->add(
            new DateInterval('P1Y')
        );

        if ($organizationStart < new DateTime()) {
            $academicYear = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\AcademicYear')
                ->findOneByStart($organizationStart);
            if (null == $academicYear) {
                $startAcademicYear = AcademicYear::getEndOfAcademicYear(
                    $organizationStart
                );
                $academicYear = new AcademicYearEntity($organizationStart, $startAcademicYear);
                $this->getEntityManager()->persist($academicYear);
                $this->getEntityManager()->flush();
            }
        }
    }
}

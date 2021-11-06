<?php

namespace SyllabusBundle\Component\Module;

use CommonBundle\Component\Util\AcademicYear;
use CommonBundle\Entity\General\AcademicYear as AcademicYearEntity;
use DateInterval;
use DateTime;

/**
 * SyllabusBundle installer
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Installer extends \CommonBundle\Component\Module\AbstractInstaller
{
    protected function postInstall()
    {
        $this->write('Installing academic year...');
        $this->installAcademicYear();
        $this->writeln(" <fg=green>\u{2713}</fg=green>", true);
    }

    private function installAcademicYear()
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

        if ($academicYear === null) {
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
            if ($academicYear == null) {
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

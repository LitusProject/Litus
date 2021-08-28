<?php

namespace CommonBundle\Controller;

use CommonBundle\Component\Util\AcademicYear as AcademicYearUtil;
use CommonBundle\Entity\General\AcademicYear;
use Laminas\View\Model\ViewModel;

/**
 * PraesidiumController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class PraesidiumController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function overviewAction()
    {
        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $academicYear = $this->getAcademicYear();

        $units = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization\Unit')
            ->findAllActiveAndDisplayed();

        $list = array();
        foreach ($units as $unit) {
            $members = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Organization\UnitMap')
                ->findAllByUnitAndAcademicYear($unit, $academicYear);
            if (isset($members[0])) {
                $list[] = array(
                    'unit'    => $unit,
                    'members' => $members,
                );
            }
        }

        $extraUnits = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization\Unit')
            ->findAllActiveAndNotDisplayed();

        $extra = array();
        foreach ($extraUnits as $unit) {
            $members = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Organization\UnitMap')
                ->findAllByUnitAndAcademicYear($unit, $academicYear);

            foreach ($members as $member) {
                if (!isset($extra[$member->getId()])) {
                    $extra[$member->getId()] = array();
                }

                $extra[$member->getId()][] = $unit;
            }
        }

        return new ViewModel(
            array(
                'units'               => $list,
                'extraUnits'          => $extra,
                'academicYears'       => $academicYears,
                'activeAcademicYear'  => $academicYear,
                'currentAcademicYear' => $this->getCurrentAcademicYear(),
                'profilePath'         => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('common.profile_path'),
            )
        );
    }

    /**
     * @return AcademicYear
     */
    private function getAcademicYear()
    {
        $date = null;
        if ($this->getParam('academicyear') !== null) {
            $date = AcademicYearUtil::getDateTime($this->getParam('academicyear'));
        }

        return AcademicYearUtil::getOrganizationYear($this->getEntityManager(), $date);
    }
}

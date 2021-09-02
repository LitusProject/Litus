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

        $units = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization\Unit')
            ->findAllActiveAndDisplayed();

        $allAcademicYears = array();

        foreach ($academicYears as $year){
            $members = array();
            foreach ($units as $unit){
                $members = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\User\Person\Organization\UnitMap')
                        ->findAllByUnitAndAcademicYear($unit, $year);
            }
            if (count($members) > 0) {
                array_push($allAcademicYears, $year);
            }
        }


        $academicYear = $this->getAcademicYear();

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
                'academicYears'       => $allAcademicYears,
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

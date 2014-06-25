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

namespace CommonBundle\Controller;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Component\Util\AcademicYear as AcademicYearUtil,
    CommonBundle\Entity\General\AcademicYear,
    Zend\View\Model\ViewModel;

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

        $academicYear = $this->_getAcademicYear();

        $units = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization\Unit')
            ->findAllActiveAndDisplayed();

        $list = array();
        foreach ($units as $unit) {
            $list[] = array(
                'unit' => $unit,
                'members' => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Organization\UnitMap')
                    ->findAllByUnitAndAcademicYear($unit, $academicYear),
            );
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
                if (!isset($extra[$member->getAcademic()->getId()]))
                    $extra[$member->getAcademic()->getId()] = array();

                $extra[$member->getAcademic()->getId()][] = $unit;
            }
        }

        return new ViewModel(
            array(
                'units' => $list,
                'extraUnits' => $extra,
                'academicYears' => $academicYears,
                'activeAcademicYear' => $academicYear,
                'profilePath' =>$this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('common.profile_path'),
            )
        );
    }

    /**
     * @return AcademicYear
     */
    private function _getAcademicYear()
    {
        $date = null;
        if (null !== $this->getParam('academicyear'))
            $date = AcademicYearUtil::getDateTime($this->getParam('academicyear'));

        return AcademicYearUtil::getUniversityYear($this->getEntityManager(), $date);
    }
}

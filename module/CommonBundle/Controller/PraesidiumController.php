<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Controller;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Component\Util\AcademicYear,
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
        foreach($units as $unit) {
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
        foreach($extraUnits as $unit) {
            $members = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Organization\UnitMap')
                ->findAllByUnitAndAcademicYear($unit, $academicYear);

            foreach($members as $member) {
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
            )
        );
    }

    private function _getAcademicYear()
    {
        $date = null;
        if (null !== $this->getParam('academicyear'))
            $date = AcademicYear::getDateTime($this->getParam('academicyear'));
        $academicYear = AcademicYear::getUniversityYear($this->getEntityManager(), $date);

        if (null === $academicYear) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No academic year was found!'
                )
            );

            $this->redirect()->toRoute(
                'common_praesidium'
            );

            return;
        }

        return $academicYear;
    }
}
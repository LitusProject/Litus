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
        $units = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization\Unit')
            ->findAllActiveAndDisplayed();

        $list = array();
        foreach($units as $unit) {
            $list[] = array(
                'unit' => $unit,
                'members' => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Organization\UnitMap')
                    ->findByUnitAndAcademicYear($unit, $this->getCurrentAcademicYear(true)),
            );
        }

        $extraUnits = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization\Unit')
            ->findAllActiveAndNotDisplayed();

        $extra = array();
        foreach($extraUnits as $unit) {
            $members = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Organization\UnitMap')
                ->findByUnitAndAcademicYear($unit, $this->getCurrentAcademicYear(true));

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
            )
        );
    }
}
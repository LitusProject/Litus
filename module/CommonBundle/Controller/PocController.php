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
use Zend\View\Model\ViewModel;

/**
 * PocController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class PocController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function overviewAction()
    {
        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();
        $academicYear = $this->getAcademicYear();
        $currentAcademicYear = $this->getCurrentAcademicYear();
        //UNCOMMENT THIS IN CASE YOU WANT TO SHOW THE POCERS OF THE LOGGED IN PERSON
        /**
        $isLoggedIn = true;
        if (!($academic = $this->getAcademicEntity())) {
            $isLoggedIn = false;
        }
        $personalPocItem = null;
        if ($isLoggedIn){
        $pocersFromAcademic = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Poc')
            ->findPocersByAcademicAndAcademicYear($academic, $currentAcademicYear);
        $personalPocItem = $this-> organisePocList($pocersFromAcademic);
        }
         */

        $pocs = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Poc')
            ->findAllByAcademicYear($academicYear);
        $pocItem = $this->organisePocList($pocs);

        return new ViewModel(
            array(
                'pocItem'             => $pocItem,
                'academicYears'       => $academicYears,
                'activeAcademicYear'  => $academicYear,
                'currentAcademicYear' => $this->getCurrentAcademicYear(),
                'profilePath'         => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('common.profile_path'),
            )
        );
    }

    public function organisePocList($pocs)
    {
        $lastPocGroup = null;
        $pocGroupList = array();

        $pocItem = array();
        foreach ($pocs as $poc) {
            $poc->setEntityManager($this->getEntityManager());

            if ($lastPocGroup === null) {
                $pocGroupList[] = $poc;
            } elseif ($lastPocGroup === $poc->getGroupId()) {
                $pocGroupList[] = $poc;
            } elseif ($lastPocGroup !== $poc->getGroupId()) {
                $pocItem[] = array(
                    'groupId'      => $lastPocGroup,
                    'pocGroupList' => $pocGroupList,
                    'pocExample'   => $pocGroupList[0],
                );

                unset($pocGroupList);

                $pocGroupList = array();
                $pocGroupList[] = $poc;
            }

            $lastPocGroup = $poc->getGroupId();
        }

        if (count($pocGroupList) > 0) {
            $pocItem[] = array(
                'groupId'      => $lastPocGroup,
                'pocGroupList' => $pocGroupList,
                'pocExample'   => $pocGroupList[0],
            );
        }

        return $pocItem;
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

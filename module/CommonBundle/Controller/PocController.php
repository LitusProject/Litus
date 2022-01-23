<?php

namespace CommonBundle\Controller;

use CommonBundle\Component\Util\AcademicYear as AcademicYearUtil;
use CommonBundle\Entity\General\AcademicYear;
use Laminas\View\Model\ViewModel;

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

        // Uncomment in case you want to show the POCers of the authenticated
        // person.

        // $currentAcademicYear = $this->getCurrentAcademicYear();
        // $isLoggedIn = true;
        // if (!($academic = $this->getAcademicEntity())) {
        //     $isLoggedIn = false;
        // }
        // $personalPocItem = null;
        // if ($isLoggedIn){
        //     $pocersFromAcademic = $this->getEntityManager()
        //         ->getRepository('SyllabusBundle\Entity\Poc')
        //         ->findPocersByAcademicAndAcademicYear($academic, $currentAcademicYear);
        //     $personalPocItem = $this-> organisePocList($pocersFromAcademic);
        // }

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

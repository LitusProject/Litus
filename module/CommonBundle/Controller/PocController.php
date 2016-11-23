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

use CommonBundle\Component\Util\AcademicYear as AcademicYearUtil,
	SyllabusBundle\Entity\Poc,
    CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\User\Person\Academic as Academic,
    SyllabusBundle\Entity\Group\StudyMap,
    SecretaryBundle\Entity\Syllabus\StudyEnrollment,
    Zend\View\Model\ViewModel;

/**
 * PocController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class PocController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function overviewAction()
    {	
		//pocers 
		
		
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
		
        $pocList = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Poc')
            ->findAllByAcademicYear($academicYear);
        $pocItem = $this->organisePocList($pocList);
		

        return new ViewModel(
            array(
            //'isLoggedIn'	=> $isLoggedIn,
            'pocItem'       => $pocItem,
			'academicYears' => $academicYears,
            'activeAcademicYear' => $academicYear,
            'currentAcademicYear' => $this->getCurrentAcademicYear(),
            'profilePath' => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('common.profile_path'),
            //'personalPocItem'	  => $personalPocItem,
            )
        );
    }
    
    public function organisePocList($pocs){
	
		$lastPocGroup = null;
        $pocGroupList = array();
        $pocItem = array();
		foreach ($pocs as $pocer){
			$pocer->setEntityManager($this->getEntityManager());
			if ($lastPocGroup === null){
				  $pocGroupList[] = $pocer;
			}

			elseif ($lastPocGroup === $pocer ->getGroupId()){
				$pocGroupList[] = $pocer;
			}
			elseif ($lastPocGroup !== $pocer ->getGroupId()){
				$pocItem[] = array(
                    'groupId' => $lastPocGroup,
                    'pocGroupList' => $pocGroupList,
                    'pocExample' => $pocGroupList[0],
                    );
                unset($pocGroupList);
                $pocGroupList = array();
                $pocGroupList[] = $pocer;
                    
			}
			$lastPocGroup = $pocer->getGroupId();
			
		 }
		 if (!empty($pocGroupList)){
			 $pocItem[] = array(
                    'groupId' => $lastPocGroup,
                    'pocGroupList' => $pocGroupList,
                    'pocExample' => $pocGroupList[0],);
			
		}
		
		return $pocItem;
		}

    /**
     * @return AcademicYear
     */
    private function getAcademicYear()
    {
        $date = null;
        if (null !== $this->getParam('academicyear')) {
            $date = AcademicYearUtil::getDateTime($this->getParam('academicyear'));
        }
        return AcademicYearUtil::getOrganizationYear($this->getEntityManager(), $date);
    }
    

     /**
     * @return Academic|null
     */
    private function getAcademicEntity()
    {
        if (!$this->getAuthentication()->isAuthenticated()) {
            return null;
        }

        $academic = $this->getAuthentication()->getPersonObject();

        if (!($academic instanceof Academic)) {
            return;
        }

        return $academic;
    }
}

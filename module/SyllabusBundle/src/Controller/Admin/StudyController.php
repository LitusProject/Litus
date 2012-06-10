<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
 
namespace SyllabusBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Component\Util\AcademicYear;

/**
 * StudyController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class StudyController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        if (!($academicYear = $this->_getAcademicYear()))
        	return;
    
        $paginator = $this->paginator()->createFromArray(
        	$this->getEntityManager()
        	    ->getRepository('SyllabusBundle\Entity\AcademicYearMap')
        	    ->findByAcademicYear($academicYear),
            $this->getParam('page')
        );
        
        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();
        
        return array(
            'academicYears' => $academicYears,
            'currentAcademicYear' => $academicYear,
        	'paginator' => $paginator,
        	'paginationControl' => $this->paginator()->createControl(true)
        );
    }
    
    public function searchAction()
    {
        $this->initAjax();
        
        if (!($academicYear = $this->_getAcademicYear()))
        	return;
        
        switch($this->getParam('field')) {
        	case 'name':
        		$mappings = $this->getEntityManager()
        			->getRepository('SyllabusBundle\Entity\AcademicYearMap')
        			->findAllByTitleAndAcademicYear($this->getParam('string'), $academicYear);
        		break;
        }
        
        $numResults = $this->getEntityManager()
        	->getRepository('CommonBundle\Entity\General\Config')
        	->getConfigValue('search_max_results');
        
        array_splice($mappings, $numResults);
        
        $result = array();
        foreach($mappings as $mapping) {
        	$item = (object) array();
        	$item->id = $mapping->getStudy()->getId();
        	$item->title = $mapping->getStudy()->getFullTitle();
        	$item->phase = $mapping->getStudy()->getPhase();
        	$result[] = $item;
        }
        
        return array(
        	'result' => $result,
        );
    }
    
    private function _getAcademicYear()
    {
        if (null === $this->getParam('academicyear')) {
    		$start = AcademicYear::getStartOfAcademicYear();
    	} else {
    	    $start = AcademicYear::getDateTime($this->getParam('academicyear'));
    	}
    	$start->setTime(0, 0);

        $academicYear = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByStartDate($start);
    	
    	if (null === $academicYear) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No academic year was found!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'admin_study',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    	
    	return $academicYear;
    }
}
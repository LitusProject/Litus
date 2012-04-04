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

use CommonBundle\Component\FlashMessenger\FlashMessage;

/**
 * SubjectController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SubjectController extends \CommonBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        if (!($study = $this->_getStudy()))
        	return;
        	
        return array(
            'study' => $study,
            'mapping' => $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\StudySubjectMap')
                ->findAllByStudy($study)
        );
    }
    
    public function subjectAction()
    {
        if (!($subject = $this->_getSubject()))
        	return;
        	
        return array(
            'subject' => $subject,
            'profs' => $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
                ->findAllBySubject($subject),
            'articles' => $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\ArticleSubjectMap')
                ->findAllBySubject($subject)
        );
    }
    
    public function searchAction()
    {
        $this->initAjax();
        
        if (!($study = $this->_getStudy()))
        	return;
        	
        switch($this->getParam('field')) {
        	case 'name':
        		$subjects = $this->getEntityManager()
        			->getRepository('SyllabusBundle\Entity\StudySubjectMap')
        			->findAllByNameAndStudy($this->getParam('string'), $study);
        		break;
        	case 'code':
        	    $subjects = $this->getEntityManager()
        	    	->getRepository('SyllabusBundle\Entity\StudySubjectMap')
        	    	->findAllByCodeAndStudy($this->getParam('string'), $study);
        	    break;
        }
        $result = array();
        foreach($subjects as $subject) {
        	$item = (object) array();
        	$item->id = $subject->getSubject()->getId();
        	$item->name = $subject->getSubject()->getName();
        	$item->code = $subject->getSubject()->getCode();
        	$item->semester = $subject->getSubject()->getSemester();
        	$item->credits = $subject->getSubject()->getCredits();
        	$item->mandatory = $subject->isMandatory();
        	$result[] = $item;
        }
        
        return array(
        	'result' => $result,
        );
    }
    
    public function typeaheadAction()
    {
        $subjects = $this->getEntityManager()
        	->getRepository('SyllabusBundle\Entity\Subject')
        	->findAllByNameTypeAhead($this->getParam('string'));
        
        $result = array();
        foreach($subjects as $subject) {
        	$item = (object) array();
        	$item->id = $subject->getId();
        	$item->value = $subject->getCode() . ' - ' . $subject->getName();
        	$result[] = $item;
        }
        
        return array(
        	'result' => $result,
        );
    }
    
    private function _getStudy()
    {
        if (null === $this->getParam('id')) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No id was given to identify the study!'
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
    
        $study = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Study')
            ->findOneById($this->getParam('id'));
    	
    	if (null === $study) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No study with the given id was found!'
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
    	
    	return $study;
    }
    
    private function _getSubject()
    {
        if (null === $this->getParam('id')) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No id was given to identify the subject!'
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
    
        $study = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Subject')
            ->findOneById($this->getParam('id'));
    	
    	if (null === $study) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No subject with the given id was found!'
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
    	
    	return $study;
    }
}
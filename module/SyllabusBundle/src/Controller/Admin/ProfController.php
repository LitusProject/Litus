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
 * ProfController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ProfController extends \CommonBundle\Component\Controller\ActionController
{
    public function subjectAction()
    {
        $subject = $this->_getSubject();
        
        return array(
            'subject' => $subject,
            'mapping' => $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
                ->findAllBySubject($subject)
        );
    }
    
    public function searchAction()
    {
        $this->initAjax();
        
        $study = $this->_getStudy();
        
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
    			'admin_prof',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    
        $subject = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Subject')
            ->findOneById($this->getParam('id'));
    	
    	if (null === $subject) {
    		$this->flashMessenger()->addMessage(
    		    new FlashMessage(
    		        FlashMessage::ERROR,
    		        'Error',
    		        'No subject with the given id was found!'
    		    )
    		);
    		
    		$this->redirect()->toRoute(
    			'admin_prof',
    			array(
    				'action' => 'manage'
    			)
    		);
    		
    		return;
    	}
    	
    	return $subject;
    }
}